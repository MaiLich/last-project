@extends('front.layout.layout')

@section('content')

    {{-- HERO giống các trang Shop/Discounted --}}
    <div class="page-style-a">
        <div class="container">
            <div class="page-intro">
                <h2>Blog</h2>
                <ul class="bread-crumb">
                    <li class="has-separator">
                        <i class="ion ion-md-home"></i>
                        <a href="{{ url('/') }}">Trang chủ</a>
                    </li>
                    <li class="is-marked">
                        <a href="javascript:;">Blog</a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    {{-- DANH SÁCH BÀI VIẾT – XẾP DỌC --}}
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                @forelse($posts as $post)
                    <article class="card border-0 shadow-sm mb-4">
                        @if($post->thumbnail)
                            <a href="{{ route('front.blog.show', $post->slug) }}">
                                <img
                                    src="{{ asset('storage/'.$post->thumbnail) }}"
                                    class="card-img-top"
                                    alt="{{ $post->title }}"
                                    style="object-fit:cover;max-height:420px;"
                                >
                            </a>
                        @endif

                        <div class="card-body">
                            <h2 class="h4 mb-2">
                                <a href="{{ route('front.blog.show', $post->slug) }}"
                                   style="color:#222;text-decoration:none;">
                                    {{ $post->title }}
                                </a>
                            </h2>

                            <p class="text-muted small mb-2">
                                {{ $post->published_at ? $post->published_at->format('d/m/Y') : '' }}
                                @if($post->author)
                                    · bởi {{ $post->author->name }}
                                @endif
                            </p>

                            <p class="mb-3">
                                {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 220) }}
                            </p>

                            <a href="{{ route('front.blog.show', $post->slug) }}" class="btn btn-outline-primary btn-sm">
                                Đọc thêm
                            </a>
                        </div>
                    </article>
                @empty
                    <p class="text-center">Chưa có bài viết nào.</p>
                @endforelse

                {{-- PHÂN TRANG --}}
                @if(method_exists($posts, 'links'))
                    <div class="mt-4">
                        {{ $posts->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
