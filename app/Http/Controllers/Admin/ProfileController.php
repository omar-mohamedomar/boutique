<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProfileUpdateRequest;
use App\Http\Requests\Admin\UpdatePasswordRequest;
use App\Models\Admin;
use App\Traits\FileUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    use FileUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::guard('admin')->user();
        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProfileUpdateRequest $request, string $id)
    {
        /** Handle image */
        $imagePath = $this->handleFileUpload($request, 'image', $request->old_image);
        /** Save Updated Datas */
        $admin = Admin::findOrFail($id);
        $admin->image = $imagePath;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->save();
        toast('Updated Successfully', 'success');
        return redirect()->back();
    }

    public function passwordUpdate(UpdatePasswordRequest $request, string $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->password = bcrypt($request->password);
        $admin->save();
        toast('Updated Successfully', 'success');
        return redirect()->back();
    }

}
