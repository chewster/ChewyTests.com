var app = angular.module('app', ['autocomplete']);

// the service that retrieves some movie title from an url
app.factory('MovieRetriever', function($http, $q, $timeout){
  var MovieRetriever = new Object();

  MovieRetriever.getmovies = function(i, target) {
    var songdata = $q.defer();
    var target = target;
    var targetURL = "https://api.spotify.com/v1/search?offset=0&limit=20&type=" + target;
    var deferred = $q.defer();
    $http.get(targetURL, {
      params: {
        query: i
      }
    }).success(function(data) {
        var songs = data.tracks.items;
        var songName = [];
        for (var i = 0; i < songs.length; i++) {
           songName = songName.concat(songs[i].name);

        };
        songdata.resolve(songName);
    });
    return songdata.promise;
  }

  return MovieRetriever;
});

app.factory('SongUploader', function($http, $q, $timeout) {
    var SongUploader = new Object();
    SongUploader.addsong = function(song) {

    }
});

app.controller('MyCtrl', function($scope, MovieRetriever){
    $scope.filter = "track";
  $scope.movies = MovieRetriever.getmovies("...");
  $scope.movies.then(function(data){
    $scope.movies = data;
  });

  $scope.getmovies = function(){
    return $scope.movies;
  }

  $scope.searchSong = function(typedthings){
    console.log("Do something like reload data with this: " + typedthings );
    $scope.newmovies = MovieRetriever.getmovies(typedthings, $scope.filter);
    console.log("getting new movies");
    console.log($scope.newmovies);
    $scope.newmovies.then(function(data){
      $scope.movies = data;
    });
  }

  $scope.doSomethingElse = function(suggestion){
    $scope.addedSongs += suggestion;
  }
  $scope.statuses = [{
      id: 1,
      name: "Low"
  }, {
      id: 2,
      name: "Normal"
  }, {
      id: 3,
      name: "High"
  }, {
      id: 4,
      name: "Urgent"
  }, {
      id: 5,
      name: "Immediate"
  }];
    $scope.filters = ["track","artist"];
    $scope.optionChange = function(test) {
        var filter = $scope.item;
        console.log("tesint the change");
        console.log(test);
        $scope.filter = test;
    }

});
