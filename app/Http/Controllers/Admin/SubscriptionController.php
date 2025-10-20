<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions
     */
    public function index()
    {
        $subscriptions = Subscription::withCount('userSubscriptions')
            ->orderBy('price', 'asc')
            ->get();

        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new subscription
     */
    public function create()
    {
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created subscription
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:subscriptions,name',
            'display_name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'duration_days' => 'required|integer|min:0',
            'max_vehicles' => 'required|integer|min:1',
            'max_reminders' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Parse features from textarea (one per line)
        if ($request->has('features_text')) {
            $features = array_filter(
                array_map('trim', explode("\n", $request->features_text))
            );
            $data['features'] = $features;
        }

        $data['is_active'] = $request->has('is_active');

        Subscription::create($data);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Подписка успешно создана');
    }

    /**
     * Display the specified subscription
     */
    public function show(Subscription $subscription)
    {
        $subscription->loadCount('userSubscriptions');
        
        // Get active subscriptions count
        $activeSubscriptions = $subscription->userSubscriptions()
            ->where('is_active', true)
            ->count();

        return view('admin.subscriptions.show', compact('subscription', 'activeSubscriptions'));
    }

    /**
     * Show the form for editing the specified subscription
     */
    public function edit(Subscription $subscription)
    {
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified subscription
     */
    public function update(Request $request, Subscription $subscription)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:subscriptions,name,' . $subscription->id,
            'display_name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'duration_days' => 'required|integer|min:0',
            'max_vehicles' => 'required|integer|min:1',
            'max_reminders' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();
        
        // Parse features from textarea
        if ($request->has('features_text')) {
            $features = array_filter(
                array_map('trim', explode("\n", $request->features_text))
            );
            $data['features'] = $features;
        }

        $data['is_active'] = $request->has('is_active');

        $subscription->update($data);

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Подписка успешно обновлена');
    }

    /**
     * Remove the specified subscription
     */
    public function destroy(Subscription $subscription)
    {
        // Prevent deletion if there are active user subscriptions
        $activeCount = $subscription->userSubscriptions()
            ->where('is_active', true)
            ->count();

        if ($activeCount > 0) {
            return redirect()->back()
                ->with('error', 'Невозможно удалить подписку, у которой есть активные пользователи (' . $activeCount . ')');
        }

        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')
            ->with('success', 'Подписка успешно удалена');
    }
}
