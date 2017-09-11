<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use DB;
use App\Post;
use App\Category;
use App\PostCategory;

class PostController extends Controller
{
    /**
     * 記事一覧を取得する。
     *
     * @param  Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $post = Post::orderBy('id', 'desc');

        if (!empty($request['category'])) {
            $category = Category::where('name', $request['category'])->first();
            if (empty($category)) abort(404);
            $postIds = PostCategory::where('category_id', $category->id)
                ->get(['post_id'])
                ->toArray();
            $ids = [];
            foreach ($postIds as $key => $value) {
                array_push($ids, $value['post_id']);
            }
            $post->whereIn('id', $ids);
        }

        $post = $post->get();
        if (empty($post)) abort(404);

        foreach ($post as $key => $value) {
            $post[$key]->categories = $this->getCategories($value->id);
        }

        return response()->json($post);
    }


    /**
     * 特定の記事を取得する。
     *
     * @param  int $id
     * @return string
     */
    public function show($id)
    {
        $post = Post::find($id);
        if (empty($post)) abort(404);

        $post->categories = $this->getCategories($post->id);

        return response()->json($post);
    }


    /**
     * 記事を作成する。
     *
     * @param  Request $request
     * @return string
     */
    public function create(Request $request)
    {
        $post = new Post;
        $post->title = $request->title;
        $post->text  = $request->text;
        if ($post->save()) {
            if (isset($request->categories) && is_array($request->categories)) {
                foreach ($request->categories as $key => $value) {
                    $PostCategory = new PostCategory;
                    $PostCategory->post_id     = $post->id;
                    $PostCategory->category_id = $value;
                    $PostCategory->save();
                }
            }
            return response()->json(true);
        } else {
            abort(501);
        }
    }


    /**
     * 記事を更新する。
     *
     * @param  Request $request
     * @param  int $id
     * @return string
     */
    public function update(Request $request, $id)
    {
        if (empty($id)) abort(404);

        $post = Post::find($id);
        if (empty($post)) abort(404);

        $post->title = $request->title;
        $post->text  = $request->text;
        if ($post->save()) {
            PostCategory::where('post_id', $post->id)->delete();
            if (isset($request->categories) && is_array($request->categories)) {
                foreach ($request->categories as $key => $value) {
                    $PostCategory = new PostCategory;
                    $PostCategory->post_id     = $post->id;
                    $PostCategory->category_id = $value;
                    $PostCategory->save();
                }
            }
            return response()->json(true);

        } else {
            abort(501);
        }
    }


    /**
     * 特定の記事が属しているカテゴリーを取得する。
     *
     * @param  int $post_id
     * @return array $categories
     */
    private function getCategories($post_id)
    {
        $query = 'select pc.post_id, pc.category_id, (select name from categories as c where c.id=pc.category_id) as category_name from posts_categories as pc where pc.post_id=?';
        $rawCategries = DB::select($query, [$post_id]);
        $categories   = [];
        if (!empty($rawCategries)) {
            foreach ($rawCategries as $key => $value) {
                array_push($categories, $value->category_name);
            }
        }
        return $categories;
    }
}