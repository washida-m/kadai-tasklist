<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Task;

class TasksController extends Controller
{
    public function index()
    {
        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザのタスク一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);
            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }

        // dashboardビューでそれらを表示
        return view('dashboard', $data);
    }

    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        $task = new Task;

        // タスク作成ビューを表示
        return view('tasks.create', [
            'task' => $task,
        ]);
    }

    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        // バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);

        // 認証済みユーザ（閲覧者）のタスクとして作成（リクエストされた値をもとに作成）
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // getでtasks/(任意のid)にアクセスされた場合の「取得表示(詳細表示)処理」
    public function show(string $id)
    {
        // idの値で投稿を検索して取得
        $task = \App\Models\Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がそのタスクの所有者である場合は,タスク詳細ビューを表示
        if (\Auth::id() === $task->user_id) {
            // タスク詳細ビューを表示
            return view('tasks.show', [
                'task' => $task,
            ]);
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // getでtasks/(任意のid)/editにアクセスされた場合の「更新画面表示処理」
    public function edit(string $id)
    {

        // idの値でタスクを検索して取得
        $task = \App\Models\Task::findOrFail($id);

        // // 認証済みユーザ（閲覧者）がそのタスクの所有者である場合は,タスク編集ビューでそれを表示
            if (\Auth::id() === $task->user_id) {
            return view('tasks.edit', [
                'task' => $task,
            ]);
            }

            // トップページへリダイレクトさせる
            return redirect('/');
    }

    // putまたはpatchでtasks/(任意のid)にアクセスされた場合の「更新処理」
    public function update(Request $request, string $id)
    {
        // バリデーション
        $request->validate([
            'content' => 'required|max:255',
            'status' => 'required|max:10',
        ]);

        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）のタスクとして更新（リクエストされた値をもとに作成）
        $task->content = $request->content;
        $task->status = $request->status;
        $task->save();

        // トップページへリダイレクトさせる
        return redirect('/');
    }

    // deleteでtasks/(任意のid)にアクセスされた場合の「削除処理」
    public function destroy(string $id)
    {
        // idの値で投稿を検索して取得
        $task = \App\Models\Task::findOrFail($id);
        
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
            return redirect('/')->with('success', 'Delete Successful');
        }

        // トップページへリダイレクトさせる
        return redirect('/')->with('Delete Failed'); 
    }
}
