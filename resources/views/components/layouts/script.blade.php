<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('gachaForm').addEventListener('submit', function(event) {
        event.preventDefault();

        axios.post('{{ route('gacha.perform') }}', {
            _token: '{{ csrf_token() }}'
        })
        .then(function(response) {
            document.getElementById('result').innerHTML = '<ul>' + response.data.map(item => '<li>' + item + '</li>').join('') + '</ul>';
        })
        .catch(function(error) {
            console.log(error);
            document.getElementById('result').innerHTML = '<p>Something went wrong. Please try again.</p>';
        });
    });
</script>
