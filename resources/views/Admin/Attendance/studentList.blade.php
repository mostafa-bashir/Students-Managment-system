@extends('masteradmin')

@section('title')
Attendance
@endsection

@section('content')
<div class="container">
    <!-- Search Bar -->
    <div class="mb-3">
        <form action="{{ route('admin.attendance') }}" method="GET">
            <input type="text" name="search" id="searchBar" class="form-control" placeholder="Search by student name..." value="{{ request('search') }}">
            <input type="hidden" name="classDate" value="{{ $date }}">
            <input type="hidden" name="className" value="{{ $classid }}">
        </form>
    </div>

    <div class="text-right mb-3">
        <button class="btn btn-success mx-5" id="attendAll">Attend All</button>
        <button class="btn btn-warning mr-5" id="absendAll">Absend All</button>
        <button class="btn btn-primary mr-5" id="clearAll">Clear All</button>
    </div>

    <form action="{{ route('admin.save.attendance') }}" method="POST" class="form-group" id="attendanceFrom">
        @csrf
        <table class="table table-bordered" id="attendanceTable">
            <thead>
                <tr>
                    <th style="width: 20%">Student Roll</th>
                    <th style="width: 40%">Name</th>
                    <th style="width: 20%" class="text-center">Attend</th>
                    <th style="width: 20%" class="text-center">Absend</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($students as $student)
                <tr>
                    <td id="{{ $student->id }}">{{ $student->id }}</td>
                    <td>{{ $student->Name . " " . $student->fatherName }}</td>
                    <td class="text-center">
                        <span id="attend{{ $student->id }}"><img src="{{ asset('customAdmin/image/attend.png') }}" alt=""
                                width="30px" class="clearAttend"></span>
                        <span id="attendance1{{ $student->id }}"><img src="{{ asset('customAdmin/image/attendance.png') }}"
                                alt="" width="30px" class="attend"></span>
                    </td>
                    <td class="text-center">
                        <span id="absend{{ $student->id }}"><img src="{{ asset('customAdmin/image/absend.png') }}" alt=""
                                width="30px" class="clearAbsend"></span>
                        <span id="attendance2{{ $student->id }}"><img src="{{ asset('customAdmin/image/attendance.png') }}"
                                alt="" width="30px" class="absend"></span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <input type="hidden" name="attendance" id="attendance">
        <input type="hidden" name="date" value="{{ $date }}">
        <input type="hidden" name="classid" value="{{ $classid }}">
        <input type="submit" value="Submit" class="btn btn-primary" id="submitAttend">
    </form>
</div>
@endsection

@section('singlePageScript')
<script>
    var attendance = {};

    // Function to update attendance data
    function updateAttendance() {
        $('#attendance').val(JSON.stringify(attendance));
    }

    // Function to handle attendance toggling
    function setupAttendanceToggle(studentId) {
        // Attend student
        $('#attend' + studentId).css('display', 'none');
        $('#attendance1' + studentId).click(function () {
            $('#attend' + studentId).css('display', 'block');
            $('#attendance1' + studentId).css('display', 'none');
            $('#absend' + studentId).css('display', 'none');
            $('#attendance2' + studentId).css('display', 'block');
            attendance[studentId] = 'attend';
            updateAttendance();
        });

        $('#attend' + studentId).click(function () {
            $('#attend' + studentId).css('display', 'none');
            $('#attendance1' + studentId).css('display', 'block');
            delete attendance[studentId];
            updateAttendance();
        });

        // Absend student
        $('#absend' + studentId).css('display', 'none');
        $('#attendance2' + studentId).click(function () {
            $('#absend' + studentId).css('display', 'block');
            $('#attendance2' + studentId).css('display', 'none');
            $('#attend' + studentId).css('display', 'none');
            $('#attendance1' + studentId).css('display', 'block');
            attendance[studentId] = 'absend';
            updateAttendance();
        });

        $('#absend' + studentId).click(function () {
            $('#absend' + studentId).css('display', 'none');
            $('#attendance2' + studentId).css('display', 'block');
            delete attendance[studentId];
            updateAttendance();
        });
    }

    // Initialize attendance toggling for all students
    $('td:first-child').each(function () {
        var studentId = $(this).attr('id');
        setupAttendanceToggle(studentId);
    });

    // Search functionality
    $('#searchBar').on('input', function () {
        var searchQuery = $(this).val().toLowerCase();
        $('#attendanceTable tbody tr').each(function () {
            var studentName = $(this).find('td:nth-child(2)').text().toLowerCase();
            if (studentName.includes(searchQuery)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Attend All, Absend All, Clear All
    var isAttend = 2;

    $('#attendAll').click(function () {
        $('.attend').click();
        isAttend = 1;
    });

    $('#absendAll').click(function () {
        $('.absend').click();
        isAttend = 0;
    });

    $('#clearAll').click(function () {
        if (isAttend == 1) {
            $('.clearAttend').click();
        } else if (isAttend == 0) {
            $('.clearAbsend').click();
        }
    });
</script>
@endsection