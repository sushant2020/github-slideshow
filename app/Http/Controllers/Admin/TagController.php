<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Http\Controllers\BaseController as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\ProductTags;
/**
 * Controller to create,edit and activate/deactivate tag functionality for user admin
 */
class TagController extends BaseController
{

    /**

     * API to list all tags created by admin
     *
     * @return json Json array of tag items
     *
     */
    public function index()
    {
        $tags = [];
        $tags = Tag::select("tags.tag_id", "name", "tags.created_at", "tags.updated_at", "tags.is_deleted", "tags.tag_id as mapped",)->get()->map(function ($tags) {
            if (!empty($tags->tag_id)) {
				$mapped = ProductTags::where("tag_id",$tags->tag_id)->whereNull("end_date")->pluck("tag_id")->first();
				
				if(!empty($mapped)) {
                $tags->mapped = true;
				} else {
					$tags->mapped = false;
				}
            };

            return $tags;
        });

        if (!empty($tags)) {
            return $this->sendResponse($tags, 'Tags fetched successfully.');
        } else {
            return $this->sendError('No Any Tag Found', ['error' => "No Any Tag Found"]);
        }
    }

    /**

     * API to create Tag
     *
     * @param \Illuminate\Http\Request $request The request
     *
     * @return json
     *
     */
    public function store(Request $request)
    {

        //Server side validations
        // $validatedData = $request->validate([
        // 	'name' => 'required|unique:tags',
        // ]);

        $validator = $this->validator($request->all());
        // $validator=$this->validator($validatedData);
        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', $errorString, 204);
        }
        //Gets request data
        $requestData = $request->all();
        $name = !empty($requestData['name']) && isset($requestData['name']) ? $requestData['name'] : "";
        $insertedby = $lastchanged_by = Auth::user()->id;
        $currentDateTime = \Carbon\Carbon::now();

        //Create Tag record
        //Tag name is UNIQUE
        try {
            $tag = Tag::create([
                        'name' => $name,
                        'created_at' => $currentDateTime,
                        'updated_at' => $currentDateTime,
                        'inserted_by' => $insertedby,
                        'lastchanged_by' => $lastchanged_by
            ]);
            return $this->sendResponse($tag->tag_id, 'Tag Created successfully.');
        } catch (\Exception $error) {
            return $this->sendError('Failed to create Tag', ['error' => "Failed : " . $error->getMessage()]);
        }
    }

    /**

     * API to update tag name
     *
     * @param $id The Tag ID
     * @param \Illuminate\Http\Request $request
     *
     * @return json
     *
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::find($id);

        if (is_null($tag)) {
            return $this->sendErrorResponse('Tag not found', 'Tag does not exist', 204);
        }

        //Server side validations

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            $errors = !empty($validator->messages()->all()) ? $validator->messages()->all() : [];
            $errorString = $this->errorString($errors);
            return $this->sendErrorResponse('Validation Error', $errorString, 204);
        }

        try {
            $tag->lastchanged_by = Auth::user()->id;
            $tag->update($request->all());

            return $this->sendResponse($tag->tag_id, 'Tag updated successfully.');
        } catch (\Exception $error) {
            return $this->sendErrorResponse('Failed to update tag', 'Failed : ' . $error->getMessage(), 204);
        }
    }

    /**

     * API to activate-deactivate Tag
     *
     * @param $id The Tag ID
     *
     * @return json
     *
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);

        if (is_null($tag)) {
            return $this->sendErrorResponse('Tag not found', 'Failed : Tag does not exist', 204);
        }

        if (!empty($tag)) {
            if ($tag->is_deleted == 1) {
                $tag->is_deleted = 0;   //if tag deactivate activate it
                $tag->updated_at = \Carbon\Carbon::now();
                $tag->lastchanged_by = Auth::user()->id;
                $message = 'Tag activated successfully.';
                $status = 'activate';
            } else {
                $tag->is_deleted = 1; //if tag activate deactivate it
                $tag->updated_at = \Carbon\Carbon::now();
                $tag->lastchanged_by = Auth::user()->id;
                $message = 'Tag deactivated successfully.';
                $status = 'deactivate';
            }
            $tag->save();
            return $this->sendResponse($status, $message);
        }
    }

    /**
     * Get a validator for an incoming change password request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
                    'name' => 'required|unique:tags'
        ]);
    }

}
