@extends('layouts.admin')
@section('page-title', 'Manajemen Santri')

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
    <p style="color:var(--text-secondary);font-size:13px;">Total: {{ $students->total() }} santri</p>
    <a href="/admin/students/create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Santri</a>
</div>

<div class="admin-card" style="padding:0;overflow:hidden;">
    <table class="data-table">
        <thead>
            <tr>
                <th>NIS</th>
                <th>Nama</th>
                <th>Kelas</th>
                <th>Kamar</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
        @foreach($students as $student)
        <tr>
            <td><strong>{{ $student->nis }}</strong></td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->class ?? '-' }}</td>
            <td>{{ $student->room ?? '-' }}</td>
            <td><span class="badge badge-{{ $student->status === 'active' ? 'success' : 'warning' }}">{{ ucfirst($student->status) }}</span></td>
            <td>
                <div style="display:flex;gap:6px;">
                    <a href="/admin/students/{{ $student->id }}/edit" class="btn btn-secondary btn-sm"><i class="fas fa-edit"></i></a>
                    <form method="POST" action="/admin/students/{{ $student->id }}" onsubmit="return confirm('Hapus santri ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>
</div>

<div class="pagination">{{ $students->links() }}</div>
@endsection
