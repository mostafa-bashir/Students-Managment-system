@extends('masteradmin')
@section('title')
    Student Attendance
@endsection

@section('content')
<style>
    th, td {
        text-align: center;
    }
</style>

<div class="mt-5 text-center">
    <h3 class="text text-primary">Attendance for {{ $student->Name }}</h3>
</div>

<div class="row">
    <div class="col-12">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Class ID</th>
                    <th>Class Name</th>
                    <th>Attendance Status</th>
                    <th>Date</th> <!-- New Date Column -->
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $data)
                <tr>
                    <td>{{ $data['class_id'] }}</td>
                    <td>{{ $data['class_name'] }}</td>
                    <td>{{ $data['attendance_status'] }}</td>
                    <td>
                        {{ $data['date'] !== 'N/A' ? \Carbon\Carbon::parse($data['date'])->format('Y-m-d') : 'N/A' }}
                    </td>
                    <td>
                        <a href="{{ route('admin.class.details', $data['class_id']) }}" class="btn btn-success">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="text-center mt-3">
    <a href="{{ route('admin.students') }}" class="btn btn-primary">Back to Student List</a>
</div>
@endsection
