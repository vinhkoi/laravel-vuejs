<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UsersController extends Controller
{
    //
    public function index(){
        $users = User::get();
        return Inertia::render("Admin/Product/UserList", ['users' => $users]);
    }
    public function update(Request $request,$id){
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->isAdmin = $request->isAdmin;
        $user->save();
        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }
    public function destroy($id){
        $user = User::findOrFail($id)->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
