<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('gachaForm').addEventListener('submit', function(e) {
        e.preventDefault();

        axios.post('/perform-gacha')
            .then(function(response) {
                const resultDiv = document.getElementById('gachaResult');
                resultDiv.innerHTML = '';

                if (response.data.success) {
                    const weapon = response.data.data;
                    resultDiv.innerHTML = `
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">You got:</span>
                            <ul class="list-disc ml-4">
                                <li>ID: ${weapon.id}</li>
                                <li>Name: ${weapon.name}</li>
                                <li>Type: ${weapon.type}</li>
                                <li>Rarity: ${weapon.rarity}</li>
                            </ul>
                        </div>
                    `;
                } else {
                    resultDiv.innerHTML = `
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">${response.data.message}</span>
                        </div>
                    `;
                }
            })
            .catch(function(error) {
                console.error('An error occurred:', error);
            });
    });
</script>
