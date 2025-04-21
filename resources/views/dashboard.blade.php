@extends('layouts.app')

@section('content')
    @if (Auth::check())
        {{-- タスク一覧 --}}
        @include('tasks.tasks')

        {{-- タスク作成ページへのリンク --}}
        <a class="btn btn-primary" href="{{ route('tasks.create') }}">新規タスクの登録</a>

    @else
        <div class="prose hero bg-base-200 mx-auto max-w-full rounded">
            <div class="hero-content text-center my-10">
                <div class="max-w-md mb-10">
                    <h2>Welcome to the Tasklist</h2>
                    {{-- ユーザ登録ページへのリンク --}}
                    <a class="btn btn-primary btn-lg normal-case" href="{{ route('register') }}">Sign up now!</a>
                </div>
            </div>
        </div>
    @endif
@endsection