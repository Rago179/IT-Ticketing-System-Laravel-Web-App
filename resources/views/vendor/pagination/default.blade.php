@if ($paginator->hasPages())
    <nav class="custom-pagination-container">
        <ul class="custom-pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled" aria-disabled="true"><span>&lsaquo; Prev</span></li>
            @else
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">&lsaquo; Prev</a></li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="disabled dots" aria-disabled="true"><span>{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="active" aria-current="page"><span>{{ $page }}</span></li>
                        @else
                            <li><a href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">Next &rsaquo;</a></li>
            @else
                <li class="disabled" aria-disabled="true"><span>Next &rsaquo;</span></li>
            @endif
        </ul>
    </nav>

    <style>
        .custom-pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            font-family: sans-serif;
        }

        .custom-pagination {
            display: flex;
            list-style: none;
            padding: 0;
            margin: 0;
            gap: 5px; /* Spacing between buttons */
            background: white;
            padding: 5px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #eee;
        }

        .custom-pagination li {
            display: flex;
        }

        .custom-pagination a, 
        .custom-pagination span {
            display: block;
            padding: 8px 14px;
            text-decoration: none;
            color: #333;
            border-radius: 4px;
            font-size: 0.95em;
            transition: background 0.2s, color 0.2s;
            min-width: 20px;
            text-align: center;
        }

        /* Hover Effect */
        .custom-pagination a:hover {
            background-color: #e2e8f0;
            color: #3490dc;
        }

        /* Active Page Style */
        .custom-pagination .active span {
            background-color: #3490dc;
            color: white;
            font-weight: bold;
            border: 1px solid #3490dc;
        }

        /* Disabled Style (Prev/Next/Dots) */
        .custom-pagination .disabled span {
            color: #ccc;
            pointer-events: none;
        }
        
        .custom-pagination .dots span {
            padding-left: 4px;
            padding-right: 4px;
            letter-spacing: 2px;
        }
    </style>
@endif