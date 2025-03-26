@extends('masteradmin')
@section('title')
    Class List
@endsection

@section('content')
<style>
    th, td {
        text-align: center;
    }
    .action-buttons {
        display: flex;
        gap: 5px;
        justify-content: center;
    }
</style>

<div class="mt-5 text-center">
    <p>
        <a href="#" class="btn btn-primary mr-3">Class Statistics</a>
        <a href="{{ route('admin.class.add') }}" class="btn btn-primary ml-3">Add New Class</a>
    </p>
</div>

<h3 class="text text-primary text-center mt-5">List of Classes</h3>

<!-- Search Bar -->
<div class="row mb-3">
    <div class="col-md-6 offset-md-3">
        <input type="text" id="classSearch" class="form-control" placeholder="Search by class name...">
    </div>
</div>

<div class="row">
    <div class="col-12">
        <table class="table table-bordered" id="classTable">
            <thead>
                <tr>
                    <th>Class ID</th>
                    <th>Name</th>
                    <th>Sessions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($classList as $class)
                <tr>
                    <td>{{ $class->id }}</td>
                    <td>{{ $class->className }}</td>
                    <td>
                        <a href="{{ route('admin.class.sessions', $class->id) }}" 
                           class="btn btn-info">
                           Manage Sessions ({{ $class->sessions_count ?? 0 }})
                        </a>
                    </td>
                    <td class="action-buttons">
                        <a href="{{ route('admin.class.details', $class->id) }}" 
                           class="btn btn-success" 
                           title="View Class Details">
                           <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.class.edit', $class->id) }}" 
                           class="btn btn-warning" 
                           title="Edit Class">
                           <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.class.delete', $class->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" title="Delete Class" onclick="return confirm('Are you sure?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@section('singlePageScript')
<script>
    $(document).ready(function() {
        // Search functionality
        $('#classSearch').on('input', function() {
            const searchText = $(this).val().toLowerCase();
            $('#classTable tbody tr').each(function() {
                const className = $(this).find('td:nth-child(2)').text().toLowerCase();
                $(this).toggle(className.includes(searchText));
            });
        });
    });
</script>
@endsection
@endsection