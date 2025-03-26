@extends('masteradmin')
@section('title') Session Attendance @endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h3>Attendance for Session: {{ $session->title }}</h3>
        <h4>Date: {{ $session->date->format('M d, Y') }}</h4>
        <h5>Class: {{ $session->class->className ?? 'General Session' }}</h5>

        <div class="row mb-3">
            <div class="col-md-6 offset-md-3">
                <div class="input-group">
                    <input type="text" id="searchStudent" class="form-control" 
                           placeholder="Search by student name..." onkeyup="searchStudents()">
                    <button type="button" class="btn btn-primary" onclick="searchStudents()">Search</button>
                    <button type="button" class="btn btn-secondary" onclick="clearSearch()">Clear</button>
                    <button type="button" class="btn btn-success" id="addStudentBtn">
                        <i class="fas fa-plus"></i> Add Student
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Add Student Modal -->
        <div class="modal fade" id="addStudentModal" tabindex="-1" aria-labelledby="addStudentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addStudentModalLabel">Add New Student</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="addStudentForm" action="{{ route('admin.students.quickAdd') }}" method="POST">
                        @csrf
                        <input type="hidden" name="session_id" value="{{ $session->id }}">
                        <div class="modal-body">
                            <div id="formErrors" class="alert alert-danger d-none"></div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="studentName" class="form-label">Full Name *</label>
                                        <input type="text" class="form-control" id="studentName" name="Name" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="studentPhone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="studentPhone" name="Phone">
                                    </div>
                                    <div class="mb-3">
                                        <label for="studentBirth" class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control" id="studentBirth" name="Birth">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-control" name="Gender">
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="studentAddress" class="form-label">Address</label>
                                        <textarea class="form-control" id="studentAddress" name="Address" rows="2"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="fatherName" class="form-label">Father's Name</label>
                                        <input type="text" class="form-control" id="fatherName" name="fatherName">
                                    </div>
                                    <div class="mb-3">
                                        <label for="fatherPhone" class="form-label">Father's Phone</label>
                                        <input type="text" class="form-control" id="fatherPhone" name="fatherPhone">
                                    </div>
                                    @if($session->class)
                                    <input type="hidden" name="class_id" value="{{ $session->class->id }}">
                                    @else
                                    <div class="mb-3">
                                        <label for="studentClass" class="form-label">Class</label>
                                        <select class="form-control" id="studentClass" name="class_id">
                                            @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->className }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Student</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Attendance Form -->
        <form action="{{ route('admin.session.attendance.save', $session->id) }}" method="POST">
            @csrf
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Current Status</th>
                        <th>Update Status</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    @foreach($students as $student)
                    @php
                        $currentStatus = isset($attendances[$student->id]) ? $attendances[$student->id]->attendance : 'absent';
                        $statusClass = [
                            'attend' => 'success',
                            'absent' => 'danger'
                        ][$currentStatus];
                    @endphp
                    <tr class="student-row">
                        <td>{{ $student->id }}</td>
                        <td class="student-name">{{ $student->Name }}</td>
                        <td>
                            <span class="badge bg-{{ $statusClass }}">
                                {{ $currentStatus === 'attend' ? 'Present' : 'Absent' }}
                            </span>
                        </td>
                        <td>
                            <select name="attendance[{{ $student->id }}]" class="form-control">
                                <option value="attend" {{ $currentStatus === 'attend' ? 'selected' : '' }}>
                                    Present
                                </option>
                                <option value="absent" {{ $currentStatus === 'absent' ? 'selected' : '' }}>
                                    Absent
                                </option>
                            </select>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary">Save Attendance</button>
        </form>
    </div>
</div>
@endsection

@section('singlePageScript')
<script>
    // Debugging initialization
    console.log("Script loaded - checking dependencies");
    console.log("jQuery version:", $.fn.jquery);
    console.log("Bootstrap Modal:", typeof $.fn.modal);
    
    // Ensure modal is properly initialized
    var addStudentModal = new bootstrap.Modal(document.getElementById('addStudentModal'));
    
    // Normalization functions
    function normalizeArabic(text) {
        const replacements = {
            'أ': 'ا', 'إ': 'ا', 'آ': 'ا', 'ٱ': 'ا',
            'ى': 'ي', 'ئ': 'ي', 'ٸ': 'ي', 
            'ة': 'ه', 'ۀ': 'ه', 'ہ': 'ه',
            'ؤ': 'و', 'ٶ': 'و',
            'َ': '', 'ُ': '', 'ِ': '', 'ْ': '', 'ّ': '', 'ـ': ''
        };
        
        return text.split('').map(c => replacements[c] || c).join('')
                  .replace(/[\u064B-\u065F}]/g, '');
    }

    // Search functionality
    function searchStudents() {
        const query = normalizeArabic($('#searchStudent').val().toLowerCase());
        
        if (query.length === 0) {
            $('.student-row').show();
            return;
        }
        
        $('.student-row').each(function() {
            const name = normalizeArabic($(this).find('.student-name').text().toLowerCase());
            $(this).toggle(name.includes(query));
        });
    }

    function clearSearch() {
        $('#searchStudent').val('');
        $('.student-row').show();
        $('#searchStudent').focus();
    }
    
    // Modal control
    $(document).ready(function() {
        // Initialize search if needed
        if ($('#searchStudent').val()) {
            searchStudents();
        }
        
        // Multiple ways to trigger modal
        $('#addStudentBtn').click(function() {
            console.log("Add Student button clicked");
            addStudentModal.show();
        });
        
        // Form submission
        $('#addStudentForm').submit(function(e) {
            e.preventDefault();
            console.log("Form submission started");
            
            // Clear previous errors
            $('#formErrors').addClass('d-none');
            
            $.ajax({
                url: $(this).attr('action'),
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    console.log("Server response:", response);
                    if(response.success) {
                        // Add new student row
                        const newRow = `
                            <tr class="student-row">
                                <td>${response.student.id}</td>
                                <td class="student-name">${response.student.Name}</td>
                                <td><span class="badge bg-success">Present</span></td>
                                <td>
                                    <select name="attendance[${response.student.id}]" class="form-control">
                                        <option value="attend" selected>Present</option>
                                        <option value="absent">Absent</option>
                                    </select>
                                </td>
                            </tr>
                        `;
                        $('#studentTableBody').append(newRow);
                        addStudentModal.hide();
                        $('#addStudentForm')[0].reset();
                        alert('Student added successfully!');
                    }
                },
                error: function(xhr) {
                    console.error("Error:", xhr.responseText);
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        let errorHtml = '';
                        for (const [field, messages] of Object.entries(errors)) {
                            errorHtml += `<p><strong>${field}:</strong> ${messages.join(', ')}</p>`;
                        }
                        $('#formErrors').html(errorHtml).removeClass('d-none');
                    } else {
                        $('#formErrors').html('<p>An unexpected error occurred. Please try again.</p>').removeClass('d-none');
                    }
                }
            });
        });
    });
</script>
@endsection