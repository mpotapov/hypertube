<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\DB;
use PDO;
use PDOException;
use Illuminate\Routing\ResponseFactory;

class MoviesController extends Controller
{

    public function index()
    {

        $client = new Client(); //GuzzleHttp\Client
        $json = $client->get('https://yts.am/api/v2/list_movies.json?sort_by=rating&limit=12')->getBody();

        $data = \GuzzleHttp\json_decode($json, true);
        $movie_count = $data['data']['movie_count'];

        $page_count = $movie_count / 12;
        $sort_by = 'like_count';
//        return $json;

        $genres = array('All', 'Action', 'Adventure',
            'Animation',
            'Biography',
            'Comedy',
            'Crime',
            'Documentary',
            'Drama',
            'Family',
            'Fantasy',
            'History',
            'Horror',
            'Music',
            'Musical',
            'Mystery',
            'Romance',
            'Sport',
            'Thriller',
            'War',
            'Western');

        $qualitys = array('All', '720p', '1080p', '3D');

        return view('movies.movies', compact('data', 'sort_by', 'page_count', 'genres', 'qualitys'));
    }

    public function getMovies(Request $request)
    {

        $page = $request->input('page');
        $sort_by = $request->input('sort');
        $order_by = $request->input('order_by');
        $quality = $request->input('quality');
        $minimum_rating = $request->input('minimum_rating');
        $query_term = $request->input('query_term');
        $genre = $request->input('genre');

        $query = '';
        if ($page != '')
            $query = $query . '&page=' . $page;
        if ($quality != '')
            $query = $query . '&quality=' . $quality;
        if ($minimum_rating != '')
            $query = $query . '&minimum_rating=' . $minimum_rating;
        if ($query_term != '')
            $query = $query . '&query_term=' . $query_term;
        if ($genre != '' && $genre != 'All')
            $query = $query . '&genre=' . $genre;
        if ($sort_by != '')
            $query = $query . '&sort_by=' . $sort_by;
        else
            $query = $query . '&sort_by=title';
        if ($order_by != '')
            $query = $query . '&order_by=' . $order_by;

        $client = new Client(); //GuzzleHttp\Client
        $json = $client->get('https://yts.am/api/v2/list_movies.json?limit=12' . $query)->getBody();

        $data = \GuzzleHttp\json_decode($json, true);
        return $data;
    }


//    public function getMovies(Request $request)
//    {
//        $params = array(
//            'host' => 'localhost',
//            'db_name' => 'hypertube',
//            'user' => 'root',
//            'password' => '123456');
//
//        try {
//            $db = new PDO("mysql:host={$params['host']};dbname={$params['db_name']}",
//                $params['user'], $params['password']);
//            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        } catch (PDOException $e) {
//            echo 'Connection failed: ' . $e->getMessage();
//        }
//
//        $page = $request->input('page');
//        $sort_by = $request->input('sort');
//        $order_by = $request->input('order_by');
////        $quality = $request->input('quality');
////        $minimum_rating = $request->input('minimum_rating');
////        $query_term = $request->input('query_term');
////        $genre = $request->input('genre');
//
//        $query = '';
////        if ($quality != '')
////            $query = $query . '&quality=' . $quality;
////        if ($minimum_rating != '')
////            $query = $query . ' AND minimal_rating BETWEEN ' . $minimum_rating .' AND 9';
////        if ($query_term != '')
////            $query = $query . '&query_term=' . $query_term;
////        if ($genre != '' && $genre != 'All')
////            $query = $query . '&genre=' . $genre;
//        if ($sort_by != '')
//            $query = $query . ' ORDER BY ' . $sort_by . ' ' . $order_by;
//        else
//            $query = $query . ' ORDER BY title ' . $order_by;
//        if ($page != '') {
//            $page -= 1;
//            $query = $query . ' LIMIT ' . (12 * $page) . ',12';
//        }
//
////        echo $request;
//
////        $client = new Client(); //GuzzleHttp\Client
////        $json = $client->get('https://yts.am/api/v2/list_movies.json?limit=12' . $query)->getBody();
//
////        echo $query;
////        $data = \GuzzleHttp\json_decode($json, true);
//
//        $sql = 'SELECT * FROM movies LIMIT 0,12';
//
//        $data = $db->prepare($sql);
//        $data->execute();
//
//        $data->setFetchMode(PDO::FETCH_ASSOC);
//
//        $data = $data->fetchAll();
////
//        return $data;
////        return response()->json($data);
//    }






















//    public function parseMovies()
//    {
//        $params = array(
//            'host' => 'localhost',
//            'db_name' => 'hypertube',
//            'user' => 'root',
//            'password' => '123456');
//
//        try {
//            $db = new PDO("mysql:host={$params['host']};dbname={$params['db_name']}",
//                $params['user'], $params['password']);
//            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        } catch (PDOException $e) {
//            echo 'Connection failed: ' . $e->getMessage();
//        }
//
//        $client = new Client(); //GuzzleHttp\Client
//
//        //  All 349 * 20 movies
//
//        for ($i = 50; $i < 349; $i++) {
//            $json = $client->get('https://yts.am/api/v2/list_movies.json?sort_by=rating&page=' . $i . '')->getBody();
////            return $json;
//            $data = \GuzzleHttp\json_decode($json, true);
//            $movies = $data['data']['movies'];
//            foreach ($movies as $movie) {
//                if (!isset($movie['torrents']) || !isset($movie['genres']))
//                    continue;
//                $sql = "SELECT COUNT(*) FROM movies WHERE id = ". $movie['id']. ";";
//                $result = $db->prepare($sql);
//                $result->execute();
//                if ($result->fetchColumn())
//                    continue;
//                $sql = "INSERT INTO movies VALUES (".$movie["id"].", :url, :imdb_code, :title, :slug, ".$movie["year"].", ".$movie["rating"].", ".$movie["runtime"].", :summary, :description_full,
//                :language, :mpa_rating, :background_image_original, :medium_cover_image, :large_cover_image)";
//                $result = $db->prepare($sql);
//
//                $result->bindParam(':url', $movie['url'], PDO::PARAM_STR);
//                $result->bindParam(':imdb_code', $movie['imdb_code'], PDO::PARAM_STR);
//                $result->bindParam(':title', $movie['title'], PDO::PARAM_STR);
//                $result->bindParam(':slug', $movie['slug'], PDO::PARAM_STR);
//                $result->bindParam(':summary', $movie['summary'], PDO::PARAM_STR);
//                $result->bindParam(':description_full', $movie['description_full'], PDO::PARAM_STR);
//                $result->bindParam(':language', $movie['language'], PDO::PARAM_STR);
//                $result->bindParam(':mpa_rating', $movie['mpa_rating'], PDO::PARAM_STR);
//                $result->bindParam(':background_image_original', $movie['background_image_original'], PDO::PARAM_STR);
//                $result->bindParam(':medium_cover_image', $movie['medium_cover_image'], PDO::PARAM_STR);
//                $result->bindParam(':large_cover_image', $movie['large_cover_image'], PDO::PARAM_STR);
//
//                $result->execute();
//                foreach ($movie['genres'] as $key => $genre) {
//                    $sql2 = "INSERT INTO genres (movie_id, genre) VALUES (" . $movie["id"] . ", :genre)";
//                    $result2 = $db->prepare($sql2);
//
//                    $result2->bindParam(':genre', $genre, PDO::PARAM_STR);
//                    $result2->execute();
//                }
//
////                print_r($movie['torrents']);
//                foreach ($movie['torrents'] as $torrent) {
//                    $sql1 = "INSERT INTO torrents (movie_id, url, hash, quality, size) VALUES (" . $movie["id"] . ", :url, :hash, :quality, :size)";
//                    $result1 = $db->prepare($sql1);
//
//                    $result1->bindParam(':url', $torrent['url'], PDO::PARAM_STR);
//                    $result1->bindParam(':hash', $torrent['hash'], PDO::PARAM_STR);
//                    $result1->bindParam(':quality', $torrent['quality'], PDO::PARAM_STR);
//                    $result1->bindParam(':size', $torrent['size'], PDO::PARAM_STR);
//                    $result1->execute();
//                }
////                echo '<pre>';
////                var_dump($movie);
//            }
//        }
//        echo 'OK' . $i;
//    }

}