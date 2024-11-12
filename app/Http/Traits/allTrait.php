<?php
namespace App\Http\Traits;
 use App\Models\Inbox;
use Illuminate\Http\Request;

// class traitControlle extends Controller
// {
    
trait allTrait{


public function destroyController($id, $model){
    
    $model::find($id)->delete();

    return response()->json(['success'=>' تم الحذف بنجاح']);
}

public function editController($id,$model) {

    $item = $model::find($id);
    return response()->json($item);
}

}
// }
