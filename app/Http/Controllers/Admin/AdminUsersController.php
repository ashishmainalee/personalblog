<?phpnamespace App\Http\Controllers\Admin;use App\Http\Controllers\Controller;use App\Http\Requests\UsersEditRequest;use App\Http\Requests\UsersRequest;use App\Photo;use App\Role;use App\User;use Illuminate\Http\Request;use Illuminate\Support\Facades\Input;use Illuminate\Support\Facades\Session;class AdminUsersController extends Controller{    /**     * Display a listing of the resource.     *     * @return \Illuminate\Http\Response     */    public function index()    {       // $users = User::whereNull('disabled') -> get();       return view('admin.users.index', compact('users'));    }    /**     * Show the form for creating a new resource.     *     * @return \Illuminate\Http\Response     */    public function create()    {       $roles      = Role::pluck('name', 'id')->all();       return view('admin.users.create', compact('roles'));    }    /**     * Store a newly created resource in storage.     *     * @param  \Illuminate\Http\Request  $request     * @return \Illuminate\Http\Response     */    public function store(UsersRequest $request)    {        // php artisan make:request UsersRequest for validation        // Replace request with UsersRequest        $input      = $request -> all();        $method     = $request -> method();        if($file = $request->file('photo_id')) {            $name = time() . $file -> getClientOriginalName();            $file->move('images/', $name);            $photo = Photo::create(['file' => $name]);            $input['photo_id'] = $photo -> id;        }        if($request -> isMethod('post')) {                $input['password']  = bcrypt(trim($request->password));                $result = User::create($input);                if($result){                    Session::flash('status', 'User Added Successfully!');                } else {                    Session::flash('error', 'Something went wrong.');                }            }        return redirect()->route('users.index');    }    /**     * Display the specified resource.     *     * @param  int  $id     * @return \Illuminate\Http\Response     */    public function show($id)    {        //    }    /**     * Show the form for editing the specified resource.     *     * @param  int  $id     * @return \Illuminate\Http\Response     */    public function edit($id)    {        $user       = User::findOrFail($id);        $roles      = Role::pluck('name', 'id')->all();        return view('admin.users.edit', compact('user','roles'));    }    /**     * Update the specified resource in storage.     *     * @param  \Illuminate\Http\Request  $request     * @param  int  $id     * @return \Illuminate\Http\Response     */    public function update(UsersEditRequest $request, $id)    {        $user = User::findOrFail($id);        $input = $request -> all();        if($file = $request -> file('photo_id') ){         $imgID = $user -> photo_id;         if($imgID){         unlink(public_path()  . $user -> photo -> file);         }         $name = time() . $file -> getClientOriginalName();         $file -> move('images/', $name);         $photo = Photo::create(['file' => $name]);         $input['photo_id'] = $photo -> id;         }         $result = $user -> update($input);         if($result) {            Session::flash('status', 'User Updated Successfully!');         } else {            Session::flash('error', 'Something went wrong.');         }         return redirect() -> route('users.index') ; }    /**     * Remove the specified resource from storage.     *     * @param  int  $id     * @return \Illuminate\Http\Response     */    public function destroy($id)    {        $user = User::findOrFail($id);        unlink(public_path()  . $user -> photo -> file);        $user->disabled = 1;        $user->save();        Session::flash('status', 'User Deleted Successfully!');        return redirect() -> route('users.index');    }    public function status(Request $request, $id)    {        $user = User::findOrFail($id);        if($user -> is_active === 1) {        $user->is_active = 0;        Session::flash('status', 'User Deactivated Successfully!');        } else {        $user->is_active = 1;        Session::flash('status', 'User Activated Successfully!');        }        $user->save();        return redirect() -> route('users.index');    }}