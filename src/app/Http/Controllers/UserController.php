<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('users.UserIndex', compact('users'));
    }

    public function edit(User $user)
    {
        $user = User::findOrFail($user->id);
        return view('users.UserEdit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $user = User::findOrFail($user->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->route('users.index')->with('success', 'ユーザー情報を更新しました。');
    }

    public function destroy(User $user)
    {
        $user = User::findOrFail($user->id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'ユーザーを削除しました。');
    }
}
