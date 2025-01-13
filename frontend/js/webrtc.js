const socket = io.connect('https://yourdomain.com:8080');
let localStream;
let peerConnection;
const config = {
    iceServers: [
        { urls: 'stun:stun.l.google.com:19302' },
        {
            urls: 'turn:openrelay.metered.ca:443',
            username: 'openrelayproject',
            credential: 'openrelayproject'
        }
    ]
};

function startCall(room) {
    navigator.mediaDevices.getUserMedia({ video: true, audio: true })
        .then(stream => {
            localStream = stream;
            document.getElementById('localVideo').srcObject = stream;

            peerConnection = new RTCPeerConnection(config);
            peerConnection.addStream(localStream);

            peerConnection.onaddstream = (event) => {
                document.getElementById('remoteVideo').srcObject = event.stream;
            };

            peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    socket.emit('signal', { room: room, candidate: event.candidate });
                }
            };

            socket.emit('join', room);
        })
        .catch(error => console.error('Error accessing media devices.', error));
}

socket.on('signal', (data) => {
    if (data.candidate) {
        peerConnection.addIceCandidate(new RTCIceCandidate(data.candidate));
    } else if (data.sdp) {
        peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp))
            .then(() => {
                if (data.sdp.type === 'offer') {
                    peerConnection.createAnswer()
                        .then(answer => {
                            peerConnection.setLocalDescription(answer);
                            socket.emit('signal', { room: data.room, sdp: answer });
                        });
                }
            });
    }
});

socket.on('user joined', (id) => {
    peerConnection.createOffer()
        .then(offer => {
            peerConnection.setLocalDescription(offer);
            socket.emit('signal', { room: room, sdp: offer });
        });
});
