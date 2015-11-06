<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Content;

class ContentController extends BaseController
{
    /**
     * Get content index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/contents');
	}
    
    /**
     * Edit content
     * 
     * @param Content $content
     * @return \Illuminate\View\View
     */
    public function form(Content $content)
	{
        if (!$content->isUserAllowed(\Auth::user())) {
            return response(view('admin/unauthorized'), 401);
        }
		return view('admin/contents-edit', compact('content'));
	}
    
    /**
     * Edit content ownership
     * 
     * @param Content $content
     * @return \Illuminate\View\View
     */
    public function formOwnership(Content $content)
	{
		return view('admin/contents-ownership', compact('content'));
	}
    
    /**
     * Save content
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        
        // Pre validation
        $input['seo_slug'] = empty($input['seo_slug']) ? str_slug($input['title']) : $input['seo_slug'];
        $input['publish_start'] = empty($input['publish_start']) ? null : $input['publish_start'];
        $input['publish_end'] = empty($input['publish_end']) ? null : $input['publish_end'];
        $input['event']['start'] = empty($input['event']['start']) ? null : $input['event']['start'];
        $input['event']['end'] = empty($input['event']['end']) ? null : $input['event']['end'];
        
        // Validate
        $validator = \Validator::make($input, [
            'title' => 'required|max:255',
            'seo_slug' => 'unique:contents'.(!empty($input['id']) ? ',seo_slug,'.$input['id'] : '')
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Load content
        if (empty($input['id'])) {
            $content = new Content;
            $content->user_id = \Auth::user()->id;
        } else {
            $content = Content::with(['event', 'location'])->find($input['id']);
        }
        
        // Validate permission
        if (!$content->isUserAllowed(\Auth::user())) {
            return response('', 401)->json(['success' => false]);
        }
        
        // Save changes
        $content->fill($input);
        $content->save();
        
        // Process relations
        $content->savePicture(\Request::file('seo_image_0'), $input['image_max_width']);
        $content->saveEvent($input['event']);
        $content->saveLocation($input['location']);
        
        // Response
        return response()->json(['success' => 'Content saved', 'redirect' => url('/admin/contents/list')]);
    }
    
    /**
     * Copy content
     * 
     * @param Content $content
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copy(Content $content)
    {
        // Create copy
        $copy = $content->replicate();
        $copy->title .= ' (copy '.date('Y-m-d H:i:s').')';
        $copy->seo_slug = str_slug($copy->title);
        $copy->save();
        
        // Copy relations to copy
        $content->copy($copy);
        
        // Redirect
        return redirect('admin/contents/form/'. $copy->id);
    }
    
    /**
     * Save content ownership
     * 
     * @param Content $content
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveOwnership(Content $content)
    {
        $input = \Input::except('_token');
        
        // Validate
        $validator = \Validator::make($input, [
            'user_id' => 'required'
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Save changes
        $content->user_id = $input['user_id'];
        $content->save();
        
        // Response
        return response()->json(['success' => 'Content saved', 'redirect' => url('/admin/contents/list')]);
    }
    
    /**
     * Upload images
     * 
     * @param Content $content
     * @return \Illiminate\Http\JsonResponse
     */
    public function upload(Content $content)
    {
        // Process seo image
        if ($file = \Request::file('image_uploader')) {
            $filename = md5(microtime()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path($content->getGalleryPath()), $filename);
            
            // Go resize if not empty
            $maxWidth = \Input::get('image_max_width');
            if (!empty($maxWidth)) {
                $content->resizeImage(public_path($content->getGalleryPath().'/'.$filename), $maxWidth);
            }
        }
        
        // Response
        return response()->json(['success' => true]);
    }
    
    /**
     * Delete image
     * 
     * @param Content $content
     * @return \Illiminate\Http\JsonResponse
     */
    public function deleteGalleryImage(Content $content, $image)
    {
        $result = unlink($content->getGalleryPath().'/'.$image);
        return response()->json(['success' => $result]);
    }
    
    /**
     * Upload attachments
     * 
     * @param Content $content
     * @return \Illiminate\Http\JsonResponse
     */
    public function uploadAttachment(Content $content)
    {
        // Process seo image
        if ($file = \Request::file('attachment_uploader')) {
            $filename = str_slug($file->getClientOriginalName()).'.'.$file->getClientOriginalExtension();
            $file->move(public_path($content->getAttachmentsPath()), $filename);
        }
        
        // Response
        return response()->json(['success' => true]);
    }
    
    /**
     * Delete attachment
     * 
     * @param Content $content
     * @return \Illiminate\Http\JsonResponse
     */
    public function deleteAttachment(Content $content, $attachment)
    {
        $result = unlink($content->getAttachmentsPath().'/'.$attachment);
        return response()->json(['success' => $result]);
    }
    
    /**
     * Get all contents
     * 
     * @param Content $content
     * @return \Illiminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Content::all()]);
	}
    
    /**
     * Delete content
     * 
     * @param Content $content
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Content $content)
    {
        $content->delete();
        return redirect('admin/contents/list');
    }

}
