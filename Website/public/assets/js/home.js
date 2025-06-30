
/*
------------------------------------------
-----------Language Switcher--------------
------------------------------------------
*/
const languageRadios = document.querySelectorAll('input[name="language"]');
const flagDisplayBanner = document.getElementById('flag-display');
const flagDisplayNav = document.getElementById('flag-display-nav'); // Get the flag display in the nav

function updateFlag(languageCode) {
    const flagUrl = languageCode === 'en'
        ? "https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Flag_of_the_United_Kingdom_%281-2%29.svg/1200px-Flag_of_the_United_Kingdom_%281-2%29.svg.png"
        : "https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Flag_of_Indonesia.svg/255px-Flag_of_Indonesia.svg.png";

    if (flagDisplayBanner) {
        flagDisplayBanner.style.backgroundImage = `url('${flagUrl}')`;
    }
    if (flagDisplayNav) {
        flagDisplayNav.style.backgroundImage = `url('${flagUrl}')`;
    }

    // Your logic to change the actual language of the website
}

languageRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        updateFlag(this.value);
        // When one radio button changes, ensure the others are also checked
        languageRadios.forEach(otherRadio => {
            otherRadio.checked = (otherRadio.value === this.value);
        });
    });
});

// Initialize flag based on the initially checked radio button
document.addEventListener('DOMContentLoaded', () => {
    const checkedRadio = document.querySelector('input[name="language"]:checked');
    if (checkedRadio) {
        updateFlag(checkedRadio.value);
    }
});

/*
------------------------------------------
--------------Realtime Clock--------------
------------------------------------------
*/ 
function updateClock() {
    const now = new Date(); // Create a new Date object to get the current date and time.
    let hours = now.getHours(); // Get the current hour (0-23).
    let minutes = now.getMinutes(); // Get the current minute (0-59).
    let seconds = now.getSeconds(); // Get the current second (0-59).
  
    // Add leading zeros if the hour, minute, or second is less than 10.
    hours = hours < 10 ? '0' + hours : hours;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    seconds = seconds < 10 ? '0' + seconds : seconds;
  
    const timeString = hours + ':' + minutes + ':' + seconds;
  
    // Get the HTML element where the clock will be displayed.
    const clockElement = document.getElementById('realtime-clock');
  
    // Update the content of the HTML element with the current time.
    clockElement.textContent = timeString;
  }
  
  // Call the updateClock function every 1000 milliseconds (1 second) to update the clock in real-time.
  setInterval(updateClock, 1000);
  
  // Optionally, you can call it once when the page loads to avoid a brief initial delay.
  updateClock();