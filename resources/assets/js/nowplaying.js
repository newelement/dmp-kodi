let kodiSettings = {};
let kodiPlaying = false;

function setKodiNowPlaying(playing) {
    axios
        .post('/api/now-playing', playing)
        .then(() => {})
        .catch(() => {});
}

function getKodiNowPlaying() {
    axios
        .get('/api/dmp-kodi-now-playing')
        .then((response) => {
            let playing = {
                poster: decodeURIComponent(
                    response.data[1].result.item.art.poster.replace('image://', '').slice(0, -1)
                ),
                contentRating: response.data[0].result.item.mpaa.replace('Rated ', ''),
                audienceRating: response.data[0].result.item.rating,
                duration: response.data[0].result.item.runtime / 60,
                mediaType: 'movie',
            };

            setKodiNowPlaying(playing);
        })
        .catch(() => {});
}

function setKodiStoppedPlaying() {
    kodiPlaying = false;
    axios
        .post('/api/stopped')
        .then(() => {})
        .catch(() => {});
}

function startKodiSocket() {
    const socket = new WebSocket(
        'ws://' + kodiSettings.kodi_url + ':' + kodiSettings.kodi_socket_port
    );

    socket.addEventListener('open', () => {});

    socket.addEventListener('message', (event) => {
        const data = JSON.parse(event.data);
        if (data.method === 'Player.OnPlay' && data.params.data.item.type === 'movie') {
            if (!kodiPlaying) {
                getKodiNowPlaying();
            }
            kodiPlaying = true;
        }

        if (data.method === 'Player.OnStop' && data.params.data.item.type === 'movie') {
            kodiPlaying = false;
            setKodiStoppedPlaying();
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    axios
        .get('/api/dmp-kodi-settings')
        .then((response) => {
            kodiSettings = response.data;
            startKodiSocket();
        })
        .catch((response) => {
            console.log(response);
            console.log('COULD NOT GET KODI SETTINGS');
        });
});
