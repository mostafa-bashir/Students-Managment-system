<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Library;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classlist;
use App\Models\Attendance;
use App\Models\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;


class adminController extends Controller
{
    public function __construct(){
        return $this->middleware('auth');
    }
    public function index(){
        $data = [];
        $data['totalStudent'] = Student::all()->count();
        $data['totalTeacher'] = Teacher::all()->count();
        $data['totalClass'] = Classlist::all()->count();
        $data['totalBook'] = Library::all()->count();

        return view('admin.dashboard', $data);
    }
    public function classSchedule(){
        return view('admin.classSchedule');
    }


    public function assignment(){
        return view('admin.assignment');
    }

    public function examlist(){
        return view('admin.examlist');
    }

    public function noticeboard(){
        return view('admin.noticeboard');
    }

    // Attendace Area

    public function attend(){
        $data['classes'] = ClassList::select('className')->get();
        return view('admin.attendance.index', $data);
    }

    public function attendList(Request $request)
    {
        // Validate required query parameters
        $validator = Validator::make($request->all(), [
            'classDate' => 'required|date',
            'className' => 'required|exists:classlists,className'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $className = $request->className;
        $date = $request->classDate;

        // Fetch the class
        $class = Classlist::where('className', $className)->first();

        // Get students in this class
        $students = Student::where('Class', $className)->get();

        // Get attendance records for this date and class
        $attendanceRecords = Attendance::where('class_id', $class->id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // Prepare student data with attendance status
        $studentData = [];
        foreach ($students as $student) {
            $studentData[] = [
                'id' => $student->id,
                'name' => $student->Name,
                'phone' => $student->Phone,
                'status' => $attendanceRecords->has($student->id) ? $attendanceRecords[$student->id]->attendance : 'Not Marked'
            ];
        }

        // Handle search
        if ($request->has('search')) {
            $search = strtolower($request->search);
            $studentData = array_filter($studentData, function($student) use ($search) {
                return strpos(strtolower($student['name']), $search) !== false;
            });
        }

        return view('admin.attendance.studentList', [
            'students' => $studentData,
            'date' => $date,
            'classid' => $class->id,
            'className' => $className
        ]);
    }

    public function saveAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'attendance' => 'required|array',
            'date' => 'required|date',
            'classid' => 'required|exists:classlists,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $date = $request->date;
        $classid = $request->classid;
        $attendanceData = $request->attendance;

        foreach ($attendanceData as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'class_id' => $classid,
                    'date' => $date
                ],
                ['attendance' => $status]
            );
        }

        return redirect()->route('admin.attendance')->with('success', 'Attendance Saved Successfully');
    }

    // Library Area
    
    public function library(){
        $data['libraryBooks'] = Library::all();
        return view('admin.library.library', $data);
    }


    public function libraryAdd(){
        return view('admin.library.addBook');
    }
    public function libraryAdded(Request $request){
        
        $request->validate([
            'bookName' => 'required',
            'bookAuthor' => 'required',
            'bookPrice' => 'required',
            'bookQuantity' => 'required',
            'bookCategory' => 'required',
            'bookStatus' => 'required'
        ]);
       
       
    
            
    
        $libraryObj = new Library();

        $libraryObj->bookName = $request->bookName;
        $libraryObj->bookAuthor = $request->bookAuthor;
        $libraryObj->bookPrice = $request->bookPrice;
        $libraryObj->bookQuantity = $request->bookQuantity;
        $libraryObj->bookQuantityAvailable = $request->bookQuantity;
        $libraryObj->bookCategory = $request->bookCategory;
        $libraryObj->bookStatus = $request->bookStatus;

        $libraryObj->save();
        return redirect()->route('admin.library')->with('success', 'Book added in Library');

    }
    
    public function libraryEdit($id){
        $data['book'] = Library::where('id', $id)->first();
        

        return view('admin.library.editBook', $data);
    }
    public function libraryUpdate($id, Request $request){
        $libraryObj = Library::find($id);

        $request->validate([
            'bookName' => 'required',
            'bookAuthor' => 'required',
            'bookPrice' => 'required',
            'bookQuantity' => 'required',
            'bookCategory' => 'required',
            'bookStatus' => 'required',
          ]);

          $libraryObj->bookName = $request->bookName;
          $libraryObj->bookAuthor = $request->bookAuthor;
          $libraryObj->bookPrice = $request->bookPrice;
          $libraryObj->bookCategory = $request->bookCategory;
          $libraryObj->bookStatus = $request->bookStatus;
  
          $libraryObj->save();
          return redirect()->route('admin.library')->with('success', 'Book Updated Successfully');
    }
    public function libraryDelete($id){
        $libraryObj = Library::find($id);
        $libraryObj->delete();
        return redirect()->route('admin.library')->with('success', 'Book Deleted Successfully');
    }

    public function calLibrary(){
        $data = [];

        $allCategory = Library::select('bookCategory')->distinct('bookCategory')->get();
        foreach($allCategory as $category){
            $category = $category->bookCategory;
            // echo $category;
        
            $count = Library::where('bookCategory', $category)->count('bookCategory');
            $categories[$category] = $count;
        }
        $data['categories'] = $categories;
        $data['totalBook'] = Library::all()->count();
        return view('admin.library.calLibrary', $data);
    }

    // student area 
    public function students(Request $request)
    {
        $query = Student::query();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $normalizedSearch = $this->normalizeArabic($search);
            
            $query->where(function($q) use ($normalizedSearch) {
                $q->whereRaw("REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(Name, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا'), 'ى', 'ي'), 'ة', 'ه'), 'ئ', 'ي'), 'ؤ', 'و'), 'ـ', '') LIKE ?", ["%{$normalizedSearch}%"])
                ->orWhere('Phone', 'LIKE', "%{$normalizedSearch}%");
            });
        }

        $data['students'] = $query->paginate(10);
        return view('admin.student.studentList', $data);
    }

    protected function normalizeArabic($text)
    {
        // Convert all variants to simple letters
        $replacements = [
            'أ' => 'ا', 'إ' => 'ا', 'آ' => 'ا', 'ٱ' => 'ا', 'ꙇ' => 'ا',
            'ى' => 'ي', 'ئ' => 'ي', 'ٸ' => 'ي', 'ۍ' => 'ي', 'ێ' => 'ي',
            'ة' => 'ه', 'ۀ' => 'ه', 'ہ' => 'ه', 'ۃ' => 'ه',
            'ؤ' => 'و', 'ٶ' => 'و', 'ۄ' => 'و', 'ۊ' => 'و',
            'ـ' => '', 'ّ' => '', // Remove tatweel and shadda
        ];
        
        // Normalize characters
        $normalized = str_replace(array_keys($replacements), array_values($replacements), $text);
        
        // Remove all remaining diacritics (harakat)
        $normalized = preg_replace('/[\x{064B}-\x{065F}]/u', '', $normalized);
        
        return $normalized;
    }
    public function studentAdd(){
        $data['class'] = ClassList::select('className')->get();
        return view('admin.student.addStudent', $data);
    }


    public function quickAdd(Request $request)
    {
        // Validate the request data
        $validated = $request->validate([
            'Name' => 'required|string|max:255',
            'Phone' => 'required|string|max:20',
            'Birth' => 'required|date',
            'Gender' => 'required|string|in:Male,Female',
            'Address' => 'required|string',
            'fatherName' => 'required|string|max:255',  // Match form field name
            'fatherPhone' => 'required|string|max:20',  // Match form field name
            'session_id' => 'required|exists:sessions,id',
            'class_id' => 'nullable'  // Add if using classes
        ]);

        // Create the student
        $student = Student::create([
            'Name' => $validated['Name'],
            'Phone' => $validated['Phone'],
            'Birth' => $validated['Birth'],
            'Gender' => $validated['Gender'],
            'Address' => $validated['Address'],
            'fatherName' => $validated['fatherName'],  // Match form field name
            'fatherPhone' => $validated['fatherPhone'],  // Match form field name
            // 'ClassID' => $validated['class_id'] ?? null  // Add if using classes
        ]);
        
        // Mark as present for this session
        // Attendance::create([
        //     'session_id' => $validated['session_id'],
        //     'student_id' => $student->id,
        //     'class_id' =>  $validated['class_id'],
        //     'attendance' => 'attend'
        // ]);

        return response()->json([
            'success' => true,
            'student' => $student
        ]);
    }
    public function importStudents(Request $request)
    {
        $request->validate([
            'student_csv' => 'required|file|mimes:csv,txt|max:2048' // 2MB max
        ]);
    
        $file = $request->file('student_csv');
        $csvData = array_map('str_getcsv', file($file->getRealPath()));
        
        $imported = 0;
        $errors = [];
        $headerSkipped = false;
    
        foreach ($csvData as $key => $row) {
            // Skip completely empty rows
            if (count(array_filter($row)) === 0) {
                continue;
            }
    
            // Skip header row if present (we'll assume first row might be headers)
            if (!$headerSkipped && $key === 0) {
                $headerSkipped = true;
                continue;
            }
    
            // Ensure minimum required fields
            if (count($row) < 3) {
                $errors[] = "Row ".($key+1).": Insufficient data (needs at least Name, Phone, Address)";
                continue;
            }
    
            $validator = Validator::make([
                'Name' => trim($row[0] ?? ''),
                'Phone' => trim($row[1] ?? ''),
                'Address' => trim($row[2] ?? ''),
                'fatherName' => trim($row[3] ?? ''),
                'fatherPhone' => trim($row[4] ?? '')
            ], [
                'Name' => 'required|string|max:255',
                'Phone' => 'required|string|max:20|unique:students,Phone',
                'Address' => 'required|string|max:255',
                'fatherName' => 'nullable|string|max:255',
                'fatherPhone' => 'nullable|string|max:20'
            ]);
    
            if ($validator->fails()) {
                $errors[] = "Row ".($key+1).": ".implode(', ', $validator->errors()->all());
                continue;
            }
    
            try {
                Student::create([
                    'Name' => $row[0],
                    'Phone' => $row[1],
                    'Address' => $row[2],
                    'Birth' => $row[3] ?? null,
                    'Gender' => $row[4] ?? null,
                    'fatherName' =>  $row[5] ?? null,
                    'fatherPhone' =>  $row[6] ?? null,
                    'Status' => 'active',
                    // Add any other default fields
                ]);
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Row ".($key+1).": Error saving student - ".$e->getMessage();
            }
        }
    
        $message = $imported > 0 
            ? "Successfully imported $imported students" 
            : "No students were imported";
        
        if (!empty($errors)) {
            $message .= " with ".count($errors)." errors";
        }
    
        return back()
            ->with('import_message', $message)
            ->with('import_errors', $errors);
    }
    public function studentAdded(Request $request){
        
        $validator = Validator::make($request->all(), [
            'Name' => 'required',
            'Phone' => 'required',
            'Birth' => 'required',
            'Gender' => 'required',
            // 'Class' => 'required',
            'Address' => 'required',
            'FatherName' => 'required',
            'FatherPhone' => 'required',
            // 'Status' => 'required'
        ]);

        

        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $studentObj = new Student();

        $studentObj->Name = $request->Name;
        $studentObj->Phone = $request->Phone;
        $studentObj->Birth = $request->Birth;
        $studentObj->Gender = $request->Gender;
        // $studentObj->Class = $request->Class;
        $studentObj->Address = $request->Address;
        $studentObj->fatherName = $request->FatherName;
        $studentObj->fatherPhone = $request->FatherPhone;
        // $studentObj->Status = $request->Status;
        
        $studentObj->save();
        return redirect()->route('admin.students')->with('success', 'Student Information Save Successfully');
    }

    public function studentDetails($id){
        $studentObj = Student::find($id);
        $data = [];
        $data['student'] = Student::find($id);
        $data['totalAttend'] = Attendance::where(['attendance' => 'attend', 'student_id' => $id] )->get()->count();
        $data['totalAbsend'] = Attendance::where(['attendance' => 'absend', 'student_id' => $id])->get()->count();
        $data['totalDays'] = Attendance::where(['student_id' => $id])->get()->count();

       return view('admin.student.studentDetails', $data);
    }

    public function studentEdit($id){
        $data['student'] = Student::where('id', $id)->first();
        $data['classes'] = Classlist::where('classStatus', 'Publish')->get();

        return view('admin.student.editStudent', $data);
    }

    public function studentUpdate(Request $request, $id){
        $studentObj = Student::find($id);

        $validator = Validator::make($request->all(), [
            'Name' => 'required',
            'Phone' => 'required',
            'Birth' => 'required',
            'Gender' => 'required',
            'Class' => 'required',
            'Address' => 'required',
            'FatherName' => 'required',
            'FatherPhone' => 'required',
            'Status' => 'required'
        ]);

        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }

        $studentObj->Name = $request->Name;
        $studentObj->Phone = $request->Phone;
        $studentObj->Birth = $request->Birth;
        $studentObj->Gender = $request->Gender;
        $studentObj->Class = $request->Class;
        $studentObj->Address = $request->Address;
        $studentObj->fatherName = $request->FatherName;
        $studentObj->fatherPhone = $request->FatherPhone;
        $studentObj->Status = $request->Status;
        
        $studentObj->save();
        return redirect()->route('admin.students')->with('success', 'Student Information Updated Successfully');

    }

    public function studentDelete($id){
        $studentObj = Student::find($id);
        $studentObj->delete();

        return redirect()->route('admin.students')->with('success', 'Student Delete Successfully');
    }

    public function studentCal(){
        $data = [];
        $data['classes'] = Classlist::all();
        $data['totalStudent'] = Student::all()->count();
        return view('admin.student.calStudent', $data);
    }


    // teacher area 

    public function teachers(){
        $data['teachers'] = Teacher::all();
        
        return view('admin.teacher.teacherList', $data);
    }
    public function teacherAdd(){
        return view('admin.teacher.addTeacher');
    }
    
    public function teacherAdded(Request $request){
        $request->validate([
            'Name' => 'required',
            'Phone' => 'required',
            'Email' => 'required',
            'Status' => 'required'
        ]);

        if($request->hasFile('photo')){
            $image = $request->file('photo');
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = public_path('/upload/teachers');
            $image->move($path, $name);

        }

        $teacherObj = new Teacher();

        $teacherObj->Name = $request->Name;
        $teacherObj->Phone = $request->Phone;
        $teacherObj->Email = $request->Email;
        $teacherObj->photo = $name;
        $teacherObj->Status = $request->Status;

        $teacherObj->save();
        return redirect()->route('admin.teachers')->with('success', 'Teacher Added Successfully');
        
    }

    public function teacherDetails($id){
        $data['teacher'] = Teacher::find($id);
        return view('admin.teacher.teacherDetails', $data);
    }

    public function teacherEdit($id){
        $data['teacher'] = Teacher::where('id', $id)->first();

        return view('admin.teacher.editTeacher', $data);
    }

    public function teacherUpdate(Request $request, $id){

        $validator = Validator::make($request->all(), [
            'Name' => 'required',
            'Phone' => 'required',
            'Email' => 'required',
            'Status' => 'required'
        ]);


        if($validator->fails()){
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $teacherObj = Teacher::find($id);
        if($request->hasFile('photo')){
            $image = $request->file('photo');
            $name = time().'.'.$image->getClientOriginalExtension();
            $path = public_path('/upload/teachers');
            $image->move($path, $name);
            $teacherObj->photo = $name;
        }

        

        $teacherObj->Name = $request->Name;
        $teacherObj->Phone = $request->Phone;
        $teacherObj->Email = $request->Email;
        
        $teacherObj->Status = $request->Status;
        $teacherObj->save();

        return redirect()->route('admin.teachers')->with('success', "Teacher Info Updated Successfully");

    }

    public function teacherDelete($id){
        $teacherObj = Teacher::find($id);
        $teacherObj->delete();

        return redirect()->route('admin.teachers')->with('success', 'Teacher Delete Successfully');
    }

    public function calTeacher(){
        $data = [];
        $data['pubTeacher'] = Teacher::where('status', 'Publish')->count();
        $data['unpubTeacher'] = Teacher::where('status', 'Unpublish')->count();
        $data['totalTeacher'] = Teacher::all()->count();

        return view('admin.teacher.calTeacher', $data);
    }

    // classlist area 

    public function classList()
    {
        $data = [];
        $data['classList'] = Classlist::withCount('sessions')->get(); 
        return view('admin.class.classList', $data);
    }
    public function classDetails($id){
        $data['class'] = classList::find($id)->first();
        return view('admin.class.classDetails', $data);
    }
    public function classAdd(){
        return view('admin.class.addClass');
    }
    public function classAdded(Request $request){
        $request->validate([
            'className' => 'required',
            // 'classQuantity' => 'required',
            // 'classStatus' => 'required'
        ]);

        $classObj = new Classlist();

        $classObj->className = $request->className;
        // $classObj->availableSeat = $request->classQuantity;
        // $classObj->totalSeat = $request->classQuantity;
        // $classObj->classStatus = $request->classStatus;

        $classObj->save();

        return redirect()->route('admin.class')->with('success', 'Class Added Successfully');


    }

    public function classEdit($id){
        $data['class'] = classList::find($id)->first();
        return view('admin.class.editClass', $data);
    }

    public function classUpdate($id, Request $request){
        $request->validate([
            'className' => 'required',
            'classQuantity' => 'required',
            'classStatus' => 'required'
        ]);
        $classObj = classList::find($id);

        $classObj->className = $request->className;
        $classObj->totalSeat = $request->classQuantity;
        $classObj->classStatus = $request->classStatus;

        $classObj->save();

        return redirect()->route('admin.class')->with('success', 'Class Edit Successfully');
    }

    public function classDelete($id){
        $classObj = classList::find($id);
        $classObj->delete();

        return redirect()->route('admin.class')->with('success', 'Class Delete Successfully');
    }

    public function downloadAttendanceCSV($classId)
    {
        // Fetch the class
        $class = ClassList::findOrFail($classId);

        // Fetch all students in the class
        $students = Student::all();

        // Fetch attendance records for the class
        $attendances = Attendance::where('class_id', $classId)->get()->keyBy('student_id');

        // Define CSV headers
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$class->className}_attendance.csv",
        ];

        // Create a callback function to generate CSV content
        $callback = function () use ($students, $attendances) {
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, ['Student ID', 'Name', 'Phone', 'Gender', 'Class', 'Attendance Status']);

            // Add rows for each student
            foreach ($students as $student) {
                $attendanceStatus = $attendances->has($student->id) ? $attendances[$student->id]->attendance : 'Not Recorded';
                fputcsv($file, [
                    $student->id,
                    $student->Name . " " . $student->fatherName,
                    $student->Phone,
                    $student->Gender,
                    $student->Class,
                    $attendanceStatus,
                ]);
            }

            fclose($file);
        };

        // Return the CSV file as a download response
        return Response::stream($callback, 200, $headers);
    }



    // showing the attendance of the student
    public function viewAttendance($studentId)
    {
        $student = Student::with(['class', 'attendances'])->findOrFail($studentId);
        
        // Get all attendance records for this student with class information
        $attendanceRecords = Attendance::where('student_id', $studentId)
            ->with('class')
            ->get()
            ->groupBy('class_id');

        // Prepare attendance data by class
        $attendanceData = [];
        foreach ($attendanceRecords as $classId => $records) {
            $class = $records->first()->class;
            
            $attendanceData[] = [
                'class_id' => $classId,
                'class_name' => $class->className,
                'total_days' => $records->count(),
                'present_days' => $records->where('attendance', 'attend')->count(),
                'absent_days' => $records->where('attendance', 'absend')->count(),
                'attendance_rate' => $records->count() > 0 
                    ? round(($records->where('attendance', 'attend')->count() / $records->count()) * 100, 2)
                    : 0
            ];
        }

        return view('admin.student.attendance', [
            'student' => $student,
            'attendanceData' => $attendanceData
        ]);
    }


    // Show sessions for a class
    public function classSessions($classId)
    {
        $class = ClassList::findOrFail($classId);
        $sessions = Session::where('class_id', $classId)
                    ->orderBy('date', 'desc')
                    ->get();
        
        return view('admin.class.sessions', compact('class', 'sessions'));
    }

    // Add new session
    public function addSession(Request $request, $classId)
    {
        $request->validate([
            'title' => 'required',
            'date' => 'required|date',
        ]);

        Session::create([
            'class_id' => $classId,
            'title' => $request->title,
            'date' => $request->date,
        ]);

        return back()->with('success', 'Session added successfully');
    }

    // Show attendance for a session
    public function sessionAttendance($sessionId)
    {
        $session = Session::with(['class', 'attendances.student'])->findOrFail($sessionId);
        
        // Get ALL students (not just those in the class)
        $allStudents = Student::all();
        
        // Get attendance records for this session
        $attendances = $session->attendances->keyBy('student_id');
        
        return view('admin.attendance.session', [
            'session' => $session,
            'students' => $allStudents,  // Now passing all students
            'attendances' => $attendances
        ]);
    }

    // Save session attendance
    public function saveSessionAttendance(Request $request, $sessionId)
    {
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:attend,absent'
        ]);

        $session = Session::findOrFail($sessionId);

        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'session_id' => $sessionId,
                    'date' => $session->date
                ],
                [
                    'attendance' => $status,
                    'class_id' => $session->class_id
                ]
            );
        }

        return back()->with('success', 'Attendance saved successfully');
    }

    // Export session attendance CSV
    public function downloadSessionAttendanceCSV($sessionId)
    {
        $session = Session::with(['class', 'attendances.student'])->findOrFail($sessionId);
        
        // Get all students with their phone and address
        $allStudents = Student::select('id', 'Name', 'Phone', 'Address', 'Class')->get();
        $attendances = $session->attendances->keyBy('student_id');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$session->class->className}_attendance.csv",
        ];

        $callback = function() use ($session, $allStudents, $attendances) {
            $file = fopen('php://output', 'w');
            
            // CSV headers with new columns
            fputcsv($file, [
                'Student ID', 
                'Name', 
                'Phone',
                'Address',
                'Class', 
                'Status', 
                'Date'
            ]);
            
            // Data rows for all students
            foreach ($allStudents as $student) {
                $attendance = $attendances[$student->id] ?? null;
                $status = $attendance ? $attendance->attendance : 'absent';
                
                fputcsv($file, [
                    $student->id,
                    $student->Name,
                    $student->Phone,          // Added phone number
                    $student->Address,        // Added address
                    $student->Class ?? 'Not Assigned',
                    $status === 'attend' ? 'Present' : 'Absent',
                    $attendance ? $attendance->date : $session->date
                ]);
            }
            
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }


    

}
