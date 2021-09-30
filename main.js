"use strict";

function dontResubmitFormWhenPageReloaded() {

  window.history.replaceState(null, null, window.location.href);
}

dontResubmitFormWhenPageReloaded();

(function () {

  let btnEventTypeClicked, eventTypeSelected, btnFieldsUpdatedClicked, fieldUpdatedSelected, btnTimestampsClicked, fromTimestamp, toTimestamp, btnSearchCombinationClicked, btnGenerateEventFileClicked, tokenCsrf;

  $('.btn:not(button[name="generateEventFile"])').on('click', event => {

    const btnClicked = $(event.currentTarget).val();
    const optionChosen = $(event.currentTarget).prev('.select-box').val();

    const eventTypes = ['Event type', 'INSERTED', 'UPDATED', 'DELETED'];
    const fieldsUpdated = ['Fields updated', 'status', 'companyUrl', 'hoursPerDay', 'overtimeRate', 'null'];

    if (btnClicked === 'btnEventType') {

      btnEventTypeClicked = btnClicked;
      btnFieldsUpdatedClicked = null;
      btnTimestampsClicked = null;
      btnSearchCombinationClicked = null;

      if (eventTypes.includes(optionChosen)) {

        eventTypeSelected = optionChosen;
        fieldUpdatedSelected = null;
        fromTimestamp = null;
        toTimestamp = null;
      }

    } else if (btnClicked === 'btnFieldsUpdated') {

      btnFieldsUpdatedClicked = btnClicked;
      btnEventTypeClicked = null;
      btnTimestampsClicked = null;
      btnSearchCombinationClicked = null;

      if (fieldsUpdated.includes(optionChosen)) {

        fieldUpdatedSelected = optionChosen;
        eventTypeSelected = null;
        fromTimestamp = null;
        toTimestamp = null;
      }

    } else if (btnClicked === 'btnTimestamps') {

      btnTimestampsClicked = btnClicked;
      btnEventTypeClicked = null;
      btnFieldsUpdatedClicked = null;
      btnSearchCombinationClicked = null;
      fieldUpdatedSelected = null;
      fromTimestamp = $('select[name="fromTimestamp"]').val();
      toTimestamp = $('select[name="toTimestamp"]').val();

    } else if (btnClicked === 'combinedQuery') {

      const combinedOptionsChosen = $(event.currentTarget).parents('#form-search-options').find('.select-box');

      btnSearchCombinationClicked = btnClicked;
      btnEventTypeClicked = null;
      btnFieldsUpdatedClicked = null;
      btnTimestampsClicked = null;

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

    tokenCsrf = $('#token-search-options').val();
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
        $('.result-content').replaceWith($('.result-content', response));
      }
    })
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
