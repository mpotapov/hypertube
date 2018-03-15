<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<style>

    .filter {
        display: flex;
        justify-content: space-around;
        margin: 20px;
    }

    .list {
        height: 100%;
    }

    .list li {
        border-bottom: 1px solid #ccc;
        display: table;
        border-collapse: collapse;
        width: 100%;
    }

    .li-img {
        display: table-cell;
        vertical-align: middle;
        width: 30%;
        padding-right: 1em;
    }

    .li-img img {
        display: block;
        width: 100%;
        height: auto;
    }

    .li-text {
        display: table-cell;
        vertical-align: middle;
        width: 70%;
    }

    .li-head {
        margin: 0;
    }

    .li-sub {
        margin: 0;
    }

    .input-group {
        margin-left: 10%;
        width: 80%;
    }

    @media all and (min-width: 45em) {
        .list li {
            float: left;
            width: 50%;
        }
    }

    @media all and (min-width: 75em) {
        .list li {
            width: 33.33333%;
        }
    }
</style>

{{--<h4 align="center">Filter</h4>--}}
<div class="filter">
    <div>
        <p>Genre:</p>
        <select name="" id="genre">
            @foreach ($genres as $genre)

                <option value="{{ $genre }}">{{ $genre }}</option>

            @endforeach
        </select>
    </div>
    <div>
        <p>Minimal rating:</p>
        <select name="" id="minimalRating">
            @for($i = 0; $i < 10; $i++)

                <option value="{{ $i }}">{{ $i }}</option>

            @endfor
        </select>
    </div>
    <div>
        <p>Quality:</p>
        <select name="" id="quality">
            @foreach ($qualitys as $quality)

                <option value="{{ $quality }}">{{ $quality }}</option>

            @endforeach
        </select>
    </div>
    <button id="filterButton" type="button" class="btn btn-primary">Search</button>
</div>

<div class="form-group">
    <div class="input-group">
        <input id="input" type="text" class="form-control" placeholder="Search film">
        <div id="search" class="input-group-addon"><a href="">Search</a></div>
    </div>
</div>

<div class="filter">
    <p>Sort</p>
    <a class="sortButton" href="" data-sort="title">Title</a>
    <a class="sortButton" href="" data-sort="year">Year</a>
    <a class="sortButton" href="" data-sort="rating">Rating</a>
</div>

<div id="pattern" class="pattern" data-page="{{ $data['data']['page_number'] }}" data-sort="{{ $sort_by }}"
     data-page_count="{{ $page_count }}" data-order_by="desc" data-quality="All" data-minimum_rating="0"
     data-genre="All" data-query_term="0">
    <ul class="list img-list">

        @foreach ($data['data']['movies'] as $movie)

            <li>
                <a href="{{ $movie['url'] }}">
                    <div class="li-img">
                        <img src="{{ $movie['medium_cover_image'] }}"/>
                    </div>
                </a>
                <div class="li-text">
                    <a href="{{ $movie['url'] }}">
                        <h4 class="li-head">{{ $movie['title'] }}</h4>
                    </a>
                    <p class="li-sub">{{ $movie['year'] }}</p>
                    <p class="li-sub">IMDb: {{ $movie['rating'] }}</p>
                </div>
            </li>

        @endforeach

    </ul>

    <ul class="pager">
        <li id="previous" class="previous"><a href="#">Previous</a></li>
        <li id="next" class="next"><a href="#">Next</a></li>
    </ul>
</div>

<script>
    $(document).ready(function () {

        if ($('#pattern').attr('data-page') == 1)
            $('#previous').hide();

        $('#search').click(function () {
            var params = {
                query_term: $('#input').val(),
                page: 1
            };
            getMovies(params);
            $('#pattern').attr('data-query_term', $('#input').val());
            return false;
        });

        $('#filterButton').click(function () {
            var params = {
                quality: $('#quality option:selected').text(),
                minimum_rating: $('#minimalRating option:selected').text(),
                genre: $('#genre option:selected').text(),
                query_term: $('#pattern').attr('data-query_term'),
                page: 1
            };
            $('#pattern').attr('data-sort', 'title');
            $('#previous').hide();
            $('#pattern').attr('data-quality', params.quality);
            $('#pattern').attr('data-minimum_rating', params.minimum_rating);
            $('#pattern').attr('data-genre', params.genre);
            getMovies(params);
        });

        $('.sortButton').click(function () {
            if ($(this).attr('data-sort') === $('#pattern').attr('data-sort')) {
                if ($('#pattern').attr('data-order_by') === 'desc')
                    $('#pattern').attr('data-order_by', 'asc');
                else if ($('#pattern').attr('data-order_by') === 'asc')
                    $('#pattern').attr('data-order_by', 'desc');
            }

            var params = {
                page: $('#pattern').attr('data-page'),
                sort: $(this).attr('data-sort'),
                order_by: $('#pattern').attr('data-order_by'),
                quality: $('#pattern').attr('data-quality'),
                minimum_rating: $('#pattern').attr('data-minimum_rating'),
                query_term: $('#pattern').attr('data-query_term'),
                genre: $('#pattern').attr('data-genre')
            };
            $('#pattern').attr('data-sort', $(this).attr('data-sort'));
            getMovies(params);
            return false;
        });

        $('#next').on('click', function () {

            var params = {
                page: parseInt($('#pattern').attr('data-page')) + 1,
                sort: $('#pattern').attr('data-sort'),
                order_by: $('#pattern').attr('data-order_by'),
                quality: $('#pattern').attr('data-quality'),
                minimum_rating: $('#pattern').attr('data-minimum_rating'),
                query_term: $('#pattern').attr('data-query_term'),
                genre: $('#pattern').attr('data-genre')
            };
            getMovies(params);
            if (params.page == $('#pattern').attr('data-page_count'))
                $('#next').hide();
            $('#previous').show();
            $('#previous').show();
        });

        $('#previous').click(function () {
            var params = {
                page: parseInt($('#pattern').attr('data-page')) - 1,
                sort: $('#pattern').attr('data-sort'),
                order_by: $('#pattern').attr('data-order_by'),
                quality: $('#pattern').attr('data-quality'),
                minimum_rating: $('#pattern').attr('data-minimum_rating'),
                query_term: $('#pattern').attr('data-query_term'),
                genre: $('#pattern').attr('data-genre')
            };
            getMovies(params);
            if (params.page == 1)
                $('#previous').hide();
            $('#next').show();
        });

        function getMovies(params) {

            $.get('api/get_movies', params, function (data) {
                $('ul.list').empty();
                $('#pattern').data('page', params.page);
                $('#pattern').attr('data-page_count', data.data.movie_count / 12);
                $.each(data.data.movies, function (index, value) {
                    $('ul.list').append("            <li>\n" +
                        "                <a href=\"" + value.url + "/" + value.id + "\">\n" +
                        "                    <div class=\"li-img\">\n" +
                        "                        <img src=\"" + value.medium_cover_image + "\" />\n" +
                        "                    </div>\n" +
                        "                </a>\n" +
                        "                    <div class=\"li-text\">\n" +
                        "                        <a href=\"" + value.url + "\">\n" +
                        "                            <h4 class=\"li-head\">" + value.title + "</h4>\n" +
                        "                        </a>\n" +
                        "                        <p class=\"li-sub\">" + value.year + "</p>\n" +
                        "                        <p class=\"li-sub\">IMDb: " + value.rating + "</p>\n" +
                        "                    </div>\n" +
                        "            </li>");
                    $('#pattern').attr('data-page', params.page);
                });
                if (parseInt($('#pattern').attr('data-page_count')) < parseInt($('#pattern').attr('data-page')))
                    $('#next').hide();
                else
                    $('#next').show();
            });
            if (params.page == 1)
                $('#previous').hide();
        }

    })
</script>