<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = Post::paginate(20);
        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::pluck('title', 'id')->all();
        $tags = Tag::pluck('title', 'id')->all();
        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'content' => 'required',
            'category_id' => 'required|integer',
            'thumbnail' => 'nullable|image',
        ]);
         
 
        //  if ($request->hasFile('yhumbnail')) {
        //      $image_name = time() . '-' .$request->image->extension();
        //      $request->yhumbnail->move(public_path('images'), $image_name);
        //      $path = 'storage/images/posts/' . $image_name;
 
        //      $validatedData['yhumbnail'] = $path;
        //      $folder = date('Y-m-d');
        //      $validatedData['yhumbnail'] = $request->file('yhumbnail')->store("{images/$folder}");
        //  }
         $validatedData['views'] = 0;
 
         
         $data = $request->all();
         $data['thumbnail'] = Post::uploadImage($request);
         $post = Post::create($data);
         $post->tags()->sync($request->tags);
         return redirect()->route('posts.index')->with('success', 'Статья добавлена');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $post = Post::find($id);
         $categories = Category::pluck('title', 'id')->all();
         $tags = Tag::pluck('title', 'id')->all();
         return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'description' => 'required',
            'content' => 'required',
            'category_id' => 'required',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
    
        $post = Post::find($id);
        $data = $request->except(['_token', '_method']);
        $data['thumbnail'] = Post::uploadImage($request, $post->thumbnail);
    
        $post->update($validated);
        $post->tags()->sync($request->tags);
        return to_route('posts.index')->with('success', 'Изменения сохранены');
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
         $post->tags()->sync([]);
         Storage::delete($post->thumbnail);
         $post->delete();
         return to_route('posts.index')->with('success', 'Статья удалена');
    }
}
