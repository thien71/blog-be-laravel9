<?php

namespace App\Http\Controllers;

use App\Http\Requests\Tag\CreateTagRequest;
use App\Http\Requests\Tag\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Http\Request;

class TagController extends Controller
{
    protected $tagService;

    public function __construct(TagService $tagService)
    {
        $this->tagService = $tagService;
    }

    public function index(Request $request)
    {
        $tags = $this->tagService->getAllTags();
        return TagResource::apiPaginate($tags, $request);
    }

    public function show($id)
    {
        $tags = $this->tagService->getTagById($id);
        return new TagResource($tags);
    }

    public function store(CreateTagRequest $request)
    {
        $tags = $this->tagService->createTag($request->validated());
        return new TagResource($tags);
    }

    public function update(UpdateTagRequest $request, $id)
    {
        $tag = Tag::findOrFail($id);
        $updatedTag = $this->tagService->updateTag($tag, $request->validated());

        return new TagResource($updatedTag);
    }

    public function destroy($id)
    {
        $tags = Tag::findOrFail($id);
        $this->tagService->deleteTag($tags);
        return response()->json(['message' => 'Tag deleted successfully']);
    }
}
