'use strict';

function dontResubmitFormWhenPageReloaded () {

  window.history.replaceState(null, null, window.location.href);
}

dontResubmitFormWhenPageReloaded();