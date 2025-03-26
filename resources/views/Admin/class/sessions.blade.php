@extends('masteradmin')
@section('title') Class Sessions @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h3>Sessions for {{ $class->className }}</h3>
        
        <div class="mb-3">
            <form action="{{ route('admin.session.add', $class->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <input type="text" name="title" class="form-control" placeholder="Session Title" required>
                    </div>
                    <div class="col-md-4">
                        <input type="date" name="date" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary">Add Session</button>
                    </div>
                </div>
            </form>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sessions as $session)
                <tr>
                    <td>{{ $session->title }}</td>
                    <td>
                        {{-- Convert the date string to Carbon instance first --}}
                        @php
                            $date = \Carbon\Carbon::parse($session->date);
                        @endphp
                        {{ $date->format('M d, Y') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.session.attendance', $session->id) }}" class="btn btn-sm btn-info">Take Attendance</a>
                        <a href="{{ route('admin.session.attendance.csv', $session->id) }}" class="btn btn-sm btn-success">Export CSV</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection