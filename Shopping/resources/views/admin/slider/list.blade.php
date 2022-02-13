@extends('admin.main')

@section('content')
<table class="table">
    <thead>
        <tr>
            <th style="width: 30px">ID</th>
            <th style="width: 250px">Tiêu Đề</th>
            <th style="width: 250px">Link</th>
            <th>Ảnh</th>
            <th>Trang Thái</th>
            <th>Update</th>
            <th>&nbsp;</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sliders as $key => $slider)
        <tr>
            <td>{{ $slider->id }}</td>
            <td>{{ $slider->name }}</td>
            <td>{{ $slider->url }}</td>
            <td><a href="{{ $slider->thumb }}" target="_blank">
                    <img src="{{ $slider->thumb }}" height="100px" width="100px">
                </a>
            </td>
            <td>{!! \App\Helpers\Helper::active($slider->active) !!}</td>
            <td>{{ $slider->updated_at }}</td>
            <td>
                <a class="btn btn-primary btn-sm" href="/admin/sliders/edit/{{ $slider->id }}">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="#" class="btn btn-danger btn-sm"
                    onclick="removeRow({{ $slider->id }}, '/admin/sliders/destroy')">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{!! $sliders->links() !!}
@endsection