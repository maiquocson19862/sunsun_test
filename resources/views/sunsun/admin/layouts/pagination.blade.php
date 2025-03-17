@if ($paginator->hasPages())
    <nav class="pagination is-centered">
        <ul class="pagination-list">
            @if ($paginator->onFirstPage())
                <!-- <a class="pagination-previous" disabled>Previous</a> -->
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="pagination-previous"><</a>
            @endif
            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li><span class="pagination-ellipsis"><span>{{ $element }}</span></span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li><a class="pagination-link is-current">{{ $page }}</a></li>
                        @else
                            <li><a href="{{ $url }}" class="pagination-link">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach
            
            <div id="inputpress" style="width: 50px; float: left; margin:0 10px; height: 25px">
                <input type="text" id="currentPage" style="width:50px; font-size:14px; height: 100%" value/>
                <input type="hidden" id="url_paginate" value="{{$paginator->path()}}">
            </div>
            @if ($paginator->hasMorePages())
                <a class="pagination-next" href="{{ $paginator->nextPageUrl() }}" rel="next">></a>
            @endif
        </ul>
    </nav>
@endif