<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Client;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::get();
        $clients = Client::when($request->search, function($q) use ($request){

            return $q->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('phone', 'like', '%' . $request->search . '%')
                ->orWhere('address', 'like', '%' . $request->search . '%');

        })->where(['type' => '1'])->latest()->paginate(5);

        return view('dashboard.clients.index', compact('clients' ,'categories' ));

    }//end of index

    public function type(Request $request)
    {
        $categories = Category::get();
        return view('dashboard.clients.type',compact('categories' ) );

    }//end of index



    public function create()
    {
        $categories = Category::get();
        return view('dashboard.clients.create',compact('categories'));

    }//end of create

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|min:1',
            'address' => 'required',
        ]);

        $request_data = $request->all();
      //  $request_data['phone'] = array_filter($request->phone);

        Client::create($request_data);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.clients.index');

    }//end of store

    public function edit(Client $client)
    {
        $categories = Category::get();
        return view('dashboard.clients.edit', compact('client','categories'));

    }//end of edit

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|array|min:1',
            'phone.0' => 'required',
            'address' => 'required',
        ]);

        $request_data = $request->all();
        $request_data['phone'] = array_filter($request->phone);

        $client->update($request_data);
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.clients.index');

    }//end of update

    public function destroy(Client $client)
    {
        $client->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.clients.index');

    }//end of destroy

}//end of controller
