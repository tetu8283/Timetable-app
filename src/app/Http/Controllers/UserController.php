<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UserController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        // userをadmin, teacher, studentの順に並び替える
        $users = User::orderByRaw("CASE role WHEN 'admin' THEN 1 WHEN 'teacher' THEN 2 WHEN 'student' THEN 3 ELSE 4 END")->get();
        return view('users.UserIndex', compact('users'));
    }

    /**
     * Summary of edit
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(User $user)
    {
        $user = User::findOrFail($user->id);
        return view('users.UserEdit', compact('user'));
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $user = User::findOrFail($user->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();
        return redirect()->route('users.index')->with('success', 'ユーザー情報を更新しました。');
    }

    /**
     * Summary of destroy
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $user = User::findOrFail($user->id);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'ユーザーを削除しました。');
    }
}

