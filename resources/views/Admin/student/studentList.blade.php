@extends('masteradmin')
@section('title')
    Student List
@endsection

@section('content')
<style>
    th, td {
        text-align: center;
    }
</style>

<div class="mt-5 text-center">
    <p>
        <button class="btn btn-primary mr-5" id="studentCalBtn" style="display: none">Student Statistics</button>
        <a href="{{ route('admin.student.add') }}" class="btn btn-primary ml-5">Add New Student</a>
    </p>
</div>

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div id="result"></div>
    </div>
</div>

<h3 class="text text-primary text-center mt-5">List of Students</h3>

<!-- Search Bar -->
<div class="row mb-3">
    <div class="col-md-6 offset-md-3">
        <input type="text" id="searchStudent" class="form-control" placeholder="Search by Name...">
    </div>
</div>

<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Attendance</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="studentTableBody">
                @foreach ($students as $student)
                <tr>
                    <td class="text-center">{{ $student->id }}</td>
                    <td>{{ $student->Name . " " .  $student->fatherName}}</td>
                    <td>{{ $student->Phone }}</td>
                    <td>
                        <a href="{{ route('admin.student.attendance', $student->id) }}" class="btn btn-info">View Attendance</a>
                    </td>
                    <td>
                        <a href="{{ route('admin.student.details', $student->id) }}" class="btn btn-success">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('singlePageScript')
<script>
    $(document).ready(function () {
        // Search Functionality
        $('#searchStudent').on('keyup', function () {
            var query = $(this).val().toLowerCase();

            $('#studentTableBody tr').each(function () {
                var name = $(this).find('td:nth-child(2)').text().toLowerCase();
                $(this).toggle(name.includes(query));
            });
        });

        // Student Statistics Button
        $('#studentCalBtn').click(function(){
            $.ajax({
                url: '{{ route('admin.student.cal') }}',
                method: 'GET',
                cache: false,
                success: function(data){
                    $('#result').html(data);
                }
            });
        });
    });
</script>
@endsection
