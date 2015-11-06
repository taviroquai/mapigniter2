<?php namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use App\Page;

class PageController extends BaseController
{
    
    /**
     * Get page index
     * 
     * @return \Illuminate\View\View
     */
    public function index()
	{
		return view('admin/pages');
	}
    
    /**
     * Edit page
     * 
     * @param Page $page
     * @return \Illuminate\View\View
     */
    public function form(Page $page)
	{
        $content = @file_get_contents($page->getViewPath());
        $data = @file_get_contents($page->getDataPath());
		return view('admin/pages-edit', compact('page', 'content', 'data'));
	}
    
    /**
     * Save page
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function save()
    {
        $input = \Input::except('_token');
        $input['name'] = strtolower($input['name']);
        
        $validator = \Validator::make($input, [
            'name' => 'required|alpha_dash|max:255|unique:pages'.(!empty($input['id']) ? ',name,'.$input['id'] : ''),
            'route' => 'required|unique:pages'.(!empty($input['id']) ? ',route,'.$input['id'] : '')
        ]);
        
        // When fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->messages()]);
        }
        
        // Fix php tag in data
        if (strpos($input['data'], '<?php') !== 0) {
            $input['data'] = '<?php '.str_replace('<?php', '', $input['data']);
        }
        
        // Save changes
        $page = empty($input['id']) ? new Page : Page::find($input['id']);
        $page->fill($input);
        $page->save();
        
        try {
            
            // Do not overwrite existing view file
            if (!empty($input['id']) 
                || (empty($input['id']) && !file_exists($page->getViewPath()))
            ) {
                file_put_contents($page->getViewPath(), $input['content']);
            }
            file_put_contents($page->getDataPath(), $input['data']);

            // Response
            return response()->json(['success' => 'Settings saved', 'redirect' => url('/admin/pages/list')]);
        } catch (\ErrorException $e) {
            return response()->json(['success' => false, 'errors' => ['permissions' => [$e->getMessage()]]]);
        }
    }
    
    /**
     * Get all pages
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function json()
	{
		return response()->json(['data' => Page::all()]);
	}
    
    /**
     * Delete page
     * 
     * @param Page $page
     * @return \Illiminate\Http\RedirectResponse
     */
    public function delete(Page $page)
    {
        $page->delete();
        return redirect('admin/pages/list');
    }

}
