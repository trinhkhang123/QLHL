<?php
  
namespace App\Http\Controllers;
  
use App\Models\Att;
use App\Models\Content;
use App\Models\Equipment;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Year;
use Illuminate\Http\Request;
use App\Models\Trainee;
 
class TraineeController extends Controller
{
    public function index()
    {
        $trainee = Trainee::with('unit')->get();
        foreach($trainee as $loop=>$tra) {
            $tra->year_id = Year::findOrFail($tra->year_id)->year;
        }
        return view('products.index', compact('trainee'));
    }
  
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $unit = Unit::where('unit_type',"Đại đội")->get();
        $year = Year::get();
        return view('products.create',compact('unit','year'));
    }
  
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Product::create($request->all());
 
        // return redirect()->route('products')->with('success', 'Product added successfully');
    }
  
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $product = Product::findOrFail($id);
  
        // return view('products.show', compact('product'));
    }
  
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $equipment = Equipment::findOrFail($id);
        $subject = Subject::get();
        $trainee = Trainee::get();
  
        return view('equipment.edit', compact('equipment','subject','trainee','id'));
    }
  
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,string $id)
    {
       $equipment = Equipment::findOrFail($id);
       $ans =  $request->all();
       if ($ans['dropdown'][1] == "Không có" || $ans['dropdown'][0] == "Không có") {
        $equipment->subject_id = -1;
        $equipment->trainee_id = -1;
       }
       else {
       $equipment->subject_id = $ans['dropdown'][1];
       $equipment->trainee_id = $ans['dropdown'][0];
    }
       $equipment->save();
       return redirect()->route('product.equipment');
    }

    public function addTB(Request $request) {
        $subject = Subject::get();
        $trainee = Trainee::get();
        return view('equipment.add',compact('subject','trainee'));
    }

    public function addEQ(Request $request) {
        $equipment = new Equipment();
        $ans =  $request->all();
        if ($ans['dropdown'][1] == "Không có" || $ans['dropdown'][0] == "Không có") {
            $equipment->subject_id = -1;
            $equipment->trainee_id = -1;
           }
           else {
           $equipment->subject_id = $ans['dropdown'][1];
           $equipment->trainee_id = $ans['dropdown'][0];
        }
        $equipment->serial_number = $ans['tb'];
        $equipment->equipment_type = $ans['cl'];
        $equipment->save();
        return redirect()->route('product.equipment');
    }


    public function updateAtt(Request $request)
    {
        $trainee = Trainee::findOrFail($request->id);
        $trainee->att ++;
        $trainee->save();
        // return redirect()->route('dashboard');;
    }

    public function unitEQ(string $id) {
        $trainee = Unit::findOrFail($id);
        $trainee->delete();
        return redirect()->route('product.unit');
    }


    public function addUNI() {
        $unit = Unit::get();
        return view('unit.add',compact('unit'));
    }

    public function addUN(Request $request) {
        $unit = new Unit();
        $ans =  $request->all();
        $unit->name = $ans['tb'];
        $unit->unit_type = $ans['cl'];
        if ($ans['dropdown'][0] != "Không có") {
            $unit->parent_unit_id =$ans['dropdown'][0]; 
        }
        $unit->save();
        return redirect()->route('product.unit');
    }

    public function unit() {
        $unit = Unit::get();
        foreach($unit as $loop=>$tt) {
            if ($tt->parent_unit_id != null) {
                $parent = Unit::findOrFail($tt->parent_unit_id);
                $unit[$loop]->parent_unit_id = $parent->name;
            }
            else 
            $unit[$loop]->parent_unit_id = "Không có";
        }
        return view('unit',compact('unit'));
    }

    public function cacu() {
        $subjects = Subject::get();
        $years = Year::get();
        $days = ['Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6','Thứ 7', 'Chủ nhật'];
        $units = Unit::get();
        return view('cacula',compact('days','subjects','years'));
    }

    public function addAtt(Request $request ) {
        $subjects = Subject::get();
        $years = Year::get();

        $att = new Content();

        $att->name = $request->name;
        $att->sesson = $request->sesson;
        $att->year_id = $years[$request->year_id-1]->id;
        $att->subject_id = $subjects[$request->subject_id-1]->id;
        $att->khoa_id = $years[$request->khoa_id-1]->id;;
        $att->save();
    }

    public function equipment() {
        $equipment = Equipment::get();
        foreach($equipment as $loop=> $eq) {
            if ($eq->subject_id != -1) {
                $subject = Subject::findOrFail($eq->subject_id);
                $equipment[$loop]->subject_id = $subject->name;
            }
            else 
            {
                $equipment[$loop]->subject_id = 'Không có';
            }
            if ($eq->trainee_id == -1) {
                $equipment[$loop]->trainee_id = 'Không có';
            }
        }
        $subject = Subject::get();
        $trainee = Trainee::get();
        return view('equipment',compact('equipment','subject','trainee'));
    }

    public function viewTKB() {
        $trainee = auth()->user()->trainee;
        $tkb = Content::where('khoa_id',$trainee->year_id)->get();
        $tksUpdate = [];
        foreach($tkb as $loop => $tk) {
            $sub = Subject::findOrFail($tk->subject_id);
            $year = Year::findOrFail($tk->year_id)->year;
            $course = Year::findOrFail($tk->year_id)->year;
            $tk->subject_name = $sub->name;
            $tk->year_name = $year;
            $tk->course_name = $course;
            $tkb[$loop] = $tk;
        }
        return view('viewTKB',compact('tkb'));
    }
  
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $trainee = Trainee::findOrFail($id);
        $user = $trainee->user();
        $trainee->delete();
        $user->delete();
        return redirect()->route('products')->with('success', 'product deleted successfully');
    }

    public function destroyEQ(string $id)
    {
        $trainee = Equipment::findOrFail($id);
        $trainee->delete();
        return redirect()->route('product.equipment');
    }

    public function dashboard() 
    {
        $unit = Unit::first();
        $id = $unit->id;
        $trainee = Trainee::with('unit')
    ->whereHas('unit', function ($query) use ($id) {
        $query->where('unit_id', $id);
    })
    ->get();
        $units = Unit::get();
        return view('dashboard',compact('trainee','units','id'));
    }
    public function dashboardCh(string $id) 
    {
        $trainee = Trainee::with('unit')
    ->whereHas('unit', function ($query) use ($id) {
        $query->where('unit_id', $id);
    })
    ->get();
        $units = Unit::get();
        return view('dashboard',compact('trainee','units','id'));
    }
}