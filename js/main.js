"use strict";

function dontResubmitFormWhenPageReloaded() {
  window.history.replaceState(null, null, window.location.href);
}
dontResubmitFormWhenPageReloaded();

(function () {

  let btnEventTypeClicked,
      eventTypeSelected,
      btnFieldsUpdatedClicked,
      fieldUpdatedSelected,
      btnTimestampsClicked,
      fromTimestamp,
      toTimestamp,
      btnSearchCombinationClicked,
      btnGenerateEventFileClicked,
      tokenCsrf;

  $('.btn:not(button[name="generateEventFile"])').on('click', event => {

    tokenCsrf = $('#token-search-options').val();
    const btnClicked = $(event.currentTarget).val();
    const optionChosen = $(event.currentTarget).prev('.select-box').val();

    const eventTypes = ['Event type', 'INSERTED', 'UPDATED', 'DELETED'];
    const fieldsUpdated = ['Fields updated', 'status', 'companyUrl', 'hoursPerDay', 'overtimeRate', 'null'];

    function getEventTypeSentToServer() {

      if (btnClicked === 'btnEventType') {
        btnEventTypeClicked = btnClicked;
        btnFieldsUpdatedClicked = btnTimestampsClicked = btnSearchCombinationClicked = null;

        if (eventTypes.includes(optionChosen)) {
          eventTypeSelected = optionChosen;
          fieldUpdatedSelected = fromTimestamp = toTimestamp = null;
        }
      }
    }

    function getFieldUpdatedSentToServer() {

      if (btnClicked === 'btnFieldsUpdated') {
        btnFieldsUpdatedClicked = btnClicked;
        btnEventTypeClicked = btnTimestampsClicked = btnSearchCombinationClicked = null;

        if (fieldsUpdated.includes(optionChosen)) {
          fieldUpdatedSelected = optionChosen;
          eventTypeSelected = fromTimestamp = toTimestamp = null;
        }
      }
    }

    function getRangeOfTimestampsSentToServer() {

      if (btnClicked === 'btnTimestamps') {
        btnTimestampsClicked = btnClicked;
        btnEventTypeClicked = btnFieldsUpdatedClicked = btnSearchCombinationClicked = fieldUpdatedSelected = null;
        fromTimestamp = $('select[name="fromTimestamp"]').val();
        toTimestamp = $('select[name="toTimestamp"]').val();
      }
    }

    function getCombinedSearchOptionsSentToServer() {

      if (btnClicked === 'combinedQuery') {
        const combinedOptionsChosen = $(event.currentTarget).parents('#form-search-options').find('.select-box');
        btnSearchCombinationClicked = btnClicked;
        btnEventTypeClicked = btnFieldsUpdatedClicked = btnTimestampsClicked = null;

        $(combinedOptionsChosen).each(function (index) {

          if (eventTypes.includes($(combinedOptionsChosen[index]).val())) {
            eventTypeSelected = $(combinedOptionsChosen[index]).val();
          }

          if (fieldsUpdated.includes($(combinedOptionsChosen[index]).val())) {
            fieldUpdatedSelected = $(combinedOptionsChosen[index]).val();
          }
        });

        fromTimestamp = $('select[name="fromTimestamp"]').val();
        toTimestamp = $('select[name="toTimestamp"]').val();
      }
    }

    getEventTypeSentToServer();
    getFieldUpdatedSentToServer();
    getRangeOfTimestampsSentToServer();
    getCombinedSearchOptionsSentToServer();
  });

  $('#form-search-options').on('submit', event => {

    event.preventDefault();

    $.ajax({
      url: 'index.php',
      method: 'post',
      data: {
        btnEventType: btnEventTypeClicked,
        eventType: eventTypeSelected,
        btnFieldsUpdated: btnFieldsUpdatedClicked,
        fieldsUpdated: fieldUpdatedSelected,
        btnTimestamps: btnTimestampsClicked,
        fromTimestamp: fromTimestamp,
        toTimestamp: toTimestamp,
        combinedQuery: btnSearchCombinationClicked,
        tokenCsrf: tokenCsrf
      },
      success(response) {
        $('.result-header').replaceWith($('.result-header', response));
        $('.result-content').replaceWith($('.result-content', response));
        fixFontSizeInSafari();
      }
    });
  });

  $('button[name="generateEventFile"]').on('click', event => {

    btnGenerateEventFileClicked = $(event.currentTarget).val();

    tokenCsrf = $('#token-generate-event-file').val();
  });

  $('#form-generate-event-file').on('submit', event => {

    event.preventDefault();

    $.ajax({
      url: 'index.php',
      method: 'post',
      data: {
        generateEventFile: btnGenerateEventFileClicked,
        tokenCsrf: tokenCsrf
      },
      success(response) {
        $('.event-file-generated-info').addClass('event-file-generated-info_visible');
        setTimeout(function () {
          $('.event-file-generated-info').addClass('event-file-generated-info_hidden');
          $('.event-file-generated-info').on('animationend', () => {
            $('.event-file-generated-info').removeClass('event-file-generated-info_hidden');
            $('.event-file-generated-info').removeClass('event-file-generated-info_visible');
          });
        }, 2500);
        $('.event-file-generated-info').off('animationend');

        $('select[name="fromTimestamp"]').replaceWith($('select[name="fromTimestamp"]', response));
        $('select[name="toTimestamp"]').replaceWith($('select[name="toTimestamp"]', response));
      }
    });
  });
})();

function fixFontSizeInSafari() {
  const isSafari = (
    (
      /Apple Computer/.test(navigator.vendor) &&
      /Safari/.test(navigator.userAgent) || /Mobile/.test(navigator.userAgent)
    )
  );
  const resultHeader = document.querySelector('.result-header');
  const resultSummaryAndContent = document.querySelectorAll('.result-summary, .result-content');
  if (isSafari) {
    resultHeader.classList.add('result-header_safari-font-smaller');
    for (const element of resultSummaryAndContent) {
      element.classList.add('result-summary-and-content');
    }
  }
}

fixFontSizeInSafari();