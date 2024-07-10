<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
    document.getElementById('gachaForm').addEventListener('submit', function(e) {
        e.preventDefault();

        axios.post('/perform-gacha')
            .then(function(response) {
                const resultDiv = document.getElementById('gachaResult');
                const pullStatus = document.getElementById('pullStatus');

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
                    pullStatus.innerHTML = `
                        <ul class="list-disc ml-4">
                            <li>Total Summons: ${weapon.totalPulls}x</li>
                            <li>Summons since last 4★ or higher: ${weapon.pitty4}</li>
                            <li>Summons since last 5★: ${weapon.pitty5}</li>
                        </ul>
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

    document.getElementById('gacha-ten-pull').addEventListener('submit', function(e) {
        e.preventDefault();

        axios.post('/perform-ten-gacha')
            .then(function(response) {
                const resultDiv = document.getElementById('gachaResult');
                const pullStatus = document.getElementById('pullStatus');

                resultDiv.innerHTML = '';

                if (response.data.success) {
                    const weapons = response.data.data;
                    let resultHTML = '<div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">';
                    resultHTML += '<span class="block sm:inline">You got:</span>';
                    resultHTML += '<ul class="list-disc ml-4">';

                    weapons.forEach(weapon => {
                        resultHTML += `
                            <li>ID: ${weapon.id}</li>
                            <li>Name: ${weapon.name}</li>
                            <li>Type: ${weapon.type}</li>
                            <li>Rarity: ${weapon.rarity}</li>
                        `;
                    });

                    resultHTML += '</ul></div>';
                    resultDiv.innerHTML = resultHTML;

                    pullStatus.innerHTML = `
                        <ul class="list-disc ml-4">
                            <li>Total Summons: ${response.data.totalPulls}x</li>
                            <li>Summons since last 4★ or higher: ${response.data.pitty4}</li>
                            <li>Summons since last 5★: ${response.data.pitty5}</li>
                        </ul>
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
