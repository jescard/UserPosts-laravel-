<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        };
        if (!$token = Auth::attempt($validator->validated())) {
            return response()->json([
                'error' => 'unauthorized'
            ], 401);
        }
        return response()->json(['token' => $token]);
    }

    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        }

        $user = User::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password)
            ]
        ));

        return response()->json([
            'message' => 'user created!',
            'user' => $user
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return response()->json(['message' => 'User logged out successfully']);
    }

    public function profile()
    {
        return response()->json(Auth::user());
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([$validator->errors()], 422);
        };

        $post = new Post;
        $post->name = $request->name;
        $post->description = $request->description;

        if (Auth::user()->posts()->save($post)) {
            return response()->json([
                'status' => true,
                'post' => $post
            ]);
        } else {
            return response()->json([
                'error' => 'Could not save the post'
            ]);
        };
    }

    public function view($id)
    {
        $post = Post::find($id);
        return $post;
    }

    public function viewAll()
    {
        $posts = Auth::user()->posts()->get();
        return response()->json($posts->toArray());
    }

    public function delete($id)
    {
        if (!$post = Post::find($id)) {
            return response()->json([
                'status' => false,
                'message' => 'Could not find post!'
            ]);
        };
        if ($post->user_id == Auth::user()->id) {
            if (Post::destroy($id)) {
                return response()->json([
                    'status' => true,
                    'message' => 'Post was deleted!'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Unable to delete post!'
                ]);
            };
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Unable to delete post!'
            ]);
        }
    }
}
