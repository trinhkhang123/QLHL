@extends('layouts.app')
  
@section('title', 'Điểm Danh')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

@section('contents')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="d-flex align-items-center justify-content-between">
        
          <select name="dropdown[]" id="dropdown1">
            @foreach($units as $rs)
            <option value="{{ $rs->id }}" {{ $rs->id == $id ? 'selected' : '' }}>
              {{ $rs->name }}
          </option>
        @endforeach
        </select>
      
    </div>
    <hr />
    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('success') }}
        </div>
    @endif
    <table class="table table-hover">
        <thead class="table-primary">
            <tr>
                <th>#</th>
                <th>Họ và tên</th>
                <th>MSSV</th>
                <th>Đơn vị</th>
                <th>Số buổi có mặt</th>
                <th>Điểm danh</th>
            </tr>
        </thead>
        <tbody>
            @if($trainee->count() > 0)
                @foreach($trainee as $rs)
                    <tr>
                        <td class="align-middle">{{ $loop->iteration }}</td>
                        <td class="align-middle">{{ $rs->full_name }}</td>
                        <td class="align-middle">{{ $rs->id }}</td>
                        
                        <td class="align-middle">{{ $rs->unit->name }}</td>  
                        <td class="align-middle">{{ $rs->att }}</td>
                        <td><input type="checkbox" name="checkbox[]" value="{{ $rs->id }}"></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="text-center" colspan="5">Không có sinh viên nào!</td>
                </tr>
            @endif
        </tbody>
    </table>
    <button class="btn btn-primary" id="btnGetValues">Xác nhận</button>
    <script>
      $(document).ready(function() {
          // Lắng nghe sự kiện khi nút được nhấn
          $('#btnGetValues').on('click', function() {
              // Tạo một mảng để lưu trữ giá trị checkbox đã chọn
              var selectedValues = [];
  
              // Duyệt qua tất cả các ô checkbox trong bảng
              $('input[name="checkbox[]"]:checked').each(function() {
                  // Thêm giá trị của checkbox đã chọn vào mảng
                  
                  $.ajax({
                  url: '{{ route("update.att") }}',
                  type: 'POST', // Chắc chắn rằng bạn đã sử dụng phương thức POST
                  data: {
                      id: $(this).val()
                  },
                  headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
                  success: function(response) {
                      // Xử lý kết quả trả về từ server
                      
                  },
                  error: function(error) {
                      // Xử lý lỗi (nếu có)
                      console.error('Error:', error);
                  }
              });
                });
                window.location.href = "/dashboard";
  
          });
      });
  </script>
    <script>
      $(document).ready(function() {
          // Bắt sự kiện change của dropdown
          $('#dropdown1').on('change', function() {
              // Lấy giá trị option được chọn
              var selectedValue = $(this).val();
              console.log(selectedValue);
              window.location.href = "/dashboard/" + selectedValue;
              $id = selectedValue;
          });
      });
  </script>
@endsection