@extends('masteradmin')
@section('title')
    Student List
@endsection

@section('content')
<style>
    th, td {
        text-align: center;
    }
    /* Improved Pagination Styling */
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .pagination li {
        margin: 0 4px;
        list-style: none;
    }
    .pagination .page-link {
        color: #007bff;
        border: 1px solid #dee2e6;
        padding: 8px 16px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
    }
    .pagination .page-item.active .page-link {
        background-color: #007bff;
        border-color: #007bff;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
    }
    .pagination .page-link:hover {
        background-color: #e9ecef;
    }
    .pagination .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0,0,0,0);
        white-space: nowrap;
        border: 0;
    }
</style>

<div class="mt-5 text-center">
    <p>
        <button class="btn btn-primary mr-5" id="studentCalBtn" style="display: none">Student Statistics</button>
        <a href="{{ route('admin.student.add') }}" class="btn btn-primary ml-5">Add New Student</a>
    </p>
</div>

<div class="row mb-4">
    <div class="col-md-6 offset-md-3">
        <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="input-group">
                <input type="file" name="student_csv" class="form-control" accept=".csv" required>
                <button type="submit" class="btn btn-success">Import CSV</button>
            </div>
            <small class="form-text text-muted">
                CSV format: Name,Phone,Address,FatherName,FatherPhone (No header row needed)
            </small>
        </form>
    </div>
</div>

@if(session('import_message'))
    <div class="alert alert-{{ session('import_errors') ? 'warning' : 'success' }}">
        {{ session('import_message') }}
    </div>
@endif

@if(session('import_errors'))
    <div class="alert alert-danger">
        <h5>Import Errors:</h5>
        <ul class="mb-0">
            @foreach(session('import_errors') as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-6 offset-md-3">
        <div id="result"></div>
    </div>
</div>

<h3 class="text text-primary text-center mt-5">List of Students</h3>


<div class="row mb-3">
    <div class="col-md-6 offset-md-3">
        <form id="searchForm" method="GET" action="{{ route('admin.students') }}">
            <div class="input-group">
                <input type="text" name="search" id="searchStudent" 
                       class="form-control" placeholder="Search by Name or Phone..."
                       value="{{ request('search') }}">
                <button type="submit" class="btn btn-primary">Search</button>
                @if(request('search'))
                    <a href="{{ route('admin.students') }}" class="btn btn-secondary">Clear</a>
                @endif
            </div>
        </form>
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
        
        <!-- Pagination Links -->
        <div class="d-flex justify-content-center mt-4">
            {{ $students->onEachSide(1)
               ->links('pagination::bootstrap-4')
               ->with('class', 'pagination-sm') }}
        </div>
    </div>
</div>
@endsection

@section('singlePageScript')
<script>
    $(document).ready(function () {
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

        // Optional: Submit form on keyup with delay
        var searchTimer;
        $('#searchStudent').on('keyup', function () {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                $('#searchForm').submit();
            }, 1000); // 0.5 second delay
        });
    });
</script>
@endsection