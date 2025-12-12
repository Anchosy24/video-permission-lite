<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers
     */
    public function index(Request $request)
    {
        $query = User::customers()->with(['accessRequests', 'activePermissions']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('is_active', $request->status === 'active' ? true : false);
        }

        $customers = $query->latest()->paginate(10);

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new customer
     */
    public function create()
    {
        return view('admin.customers.form', ['customer' => null]);
    }

    /**
     * Store a newly created customer
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'customer',
                'is_active' => $request->is_active,
            ]);

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show the form for editing the customer
     */
    public function edit(User $customer)
    {
        // Pastikan yang diedit adalah customer, bukan admin
        if ($customer->isAdmin()) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Tidak dapat mengedit data admin');
        }

        return view('admin.customers.form', compact('customer'));
    }

    /**
     * Update the customer
     */
    public function update(Request $request, User $customer)
    {
        // Pastikan yang diupdate adalah customer
        if ($customer->isAdmin()) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Tidak dapat mengubah data admin');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($customer->id),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'required|boolean',
        ], [
            'name.required' => 'Nama harus diisi',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah terdaftar',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->is_active,
            ];

            // Update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $customer->update($data);

            return redirect()->route('admin.customers.index')
                ->with('success', 'Customer berhasil diperbarui');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the customer
     */
    public function destroy(User $customer)
    {
        // Pastikan yang dihapus adalah customer
        if ($customer->isAdmin()) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Tidak dapat menghapus data admin');
        }

        try {
            $name = $customer->name;
            $customer->delete();

            return redirect()->route('admin.customers.index')
                ->with('success', "Customer {$name} berhasil dihapus");

        } catch (\Exception $e) {
            return redirect()->route('admin.customers.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}