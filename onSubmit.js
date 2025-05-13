const CAPTCHA_SITE_KEY = '6LfAjTcrAAAAAJ4CaFltGfPXKrHb9NFk81MpQfZY';
const CAPTCHA_PROJECT_ID = 'web1-22810215-1747093310606';
const CAPTCHA_API_KEY = 'AIzaSyC8VPNI5APz2N36K3kYynej7bu82bDs4pw';

function onSubmit(e) {
    e.preventDefault();

    let responseMessage = document.getElementById('response');
    responseMessage.className = 'text-success';
    responseMessage.innerHTML = '';

    grecaptcha.enterprise.ready(async function () {
        let token = await grecaptcha.enterprise.execute(CAPTCHA_SITE_KEY, { action: 'submit' });

        let url = `https://recaptchaenterprise.googleapis.com/v1/projects/${CAPTCHA_PROJECT_ID}/assessments?key=${CAPTCHA_API_KEY}`;

        let postData = {
            "event": {
                "token": token,
                "siteKey": CAPTCHA_SITE_KEY,
                "expectedAction": "submit"
            }
        };
        let response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(postData)
        });

        let result = await response.json();

        if (result.tokenProperties.valid) {
            sendMail();
        } else {
            responseMessage.innerHTML = 'Error: Can not verify Recaptcha!';
            responseMessage.className = 'text-danger';
        }

    });
}