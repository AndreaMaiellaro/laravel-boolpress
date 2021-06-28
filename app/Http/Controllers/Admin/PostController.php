<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Post;
use App\Category;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::all();

        $data = [
            'posts' => $posts
        ];

        return view('admin.posts.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $new_post_data = $request->all();

        //create slug
        $new_slug = Str::slug($new_post_data['title'], '-');
        $base_slug = $new_slug;
        // Controlliamo che non esista un post con questo slug
        $post_with_existing_slug = Post::where('slug', '=', $new_slug)->first();
        $counter = 1;

        // Se esiste tento con altri slug
        while($post_with_existing_slug) {
            // Provo on un nuovo slug appendendo il counter
            $new_slug = $base_slug . '-' . $counter;
            $counter++;

            // Se nuovo slug esiste nel db, il while continua
            $post_with_existing_slug = Post::where('slug', '=', $new_slug)->first();
        }

        //slug libero, popoliamo i data da salvare
        $new_post_data['slug'] = $new_slug;

        $new_post = new Post();
        $new_post->fill($new_post_data);
        $new_post->save();

        return redirect()->route('admin.posts.show', ['post' => $new_post->id]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $post = Post::findOrFail($id);

        $data = [
            'post' => $post
        ];

        return view('admin.posts.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $categories = Category::all();
        $tags = Tag::all();

        $data = [
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags
        ];

        return view('admin.posts.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        {
            $request->validate([
                'title' => 'required|max:255',
                'content' => 'required|max:65000',
                'category_id' => 'nullable|exists:categories,id',
                'tags' => 'nullable|exists:tags,id'
            ]);
    
            $modified_post_data = $request->all();
    
            $post = Post::findOrFail($id);
    
            // Di default lo slug non dovrebbe essere cambiamo a meno che cambi il titolo del post
            $modified_post_data['slug'] = $post->slug;
    
            // Se il titolo cambia allora ricalcolo lo slug
            if($modified_post_data['title'] != $post->title) {
                // Creiamo lo slug
                $new_slug = Str::slug($modified_post_data['title'], '-');
                $base_slug = $new_slug;
                // Controlliamo che non esista un post con questo slug
                $post_with_existing_slug = Post::where('slug', '=', $new_slug)->first();
                $counter = 1;
    
                // Se esiste tento con altri slug
                while($post_with_existing_slug) {
                    // Provo on un nuovo slug appendendo il counter
                    $new_slug = $base_slug . '-' . $counter;
                    $counter++;
    
                    // Se anche il nuovo slug esiste nel db, il while continua...
                    $post_with_existing_slug = Post::where('slug', '=', $new_slug)->first();
                }
    
                // Quando finalmente troviamo uno slug libero, popoliamo i data da salvare
                $modified_post_data['slug'] = $new_slug;
            }
    
            $post->update($modified_post_data);
    
            // Tags
            if(isset($modified_post_data['tags']) && is_array($modified_post_data['tags'])) {
                $post->tags()->sync($modified_post_data['tags']);
            } else {
                $post->tags()->sync([]);
            }
            
            return redirect()->route('admin.posts.show', ['post' => $post->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->tags()->sync([]);
        $post->delete();

        return redirect()->route('admin.posts.index');
    }
}
