"use strict";

function dontResubmitFormWhenPageReloaded() {
  window.history.replaceState(null, null, window.location.href);
}

dontResubmitFormWhenPageReloaded();

/*function submitForm (event) {
  event.preventDefault();
  const elements = document.querySelectorAll('.select-box');
  console.log(elements)
  const formData = new FormData();

  for (const element of elements) {

    formData.append(element.name, element.value);
  }
  console.log(formData)
  const xmlHttp = new XMLHttpRequest();
  xmlHttp.onreadystatechange = function () {
    if(xmlHttp.readyState == 4 && xmlHttp.status == 200) {
    }
  }
  xmlHttp.open("post", "index.php");
  xmlHttp.send(formData);

}

document.querySelector('#search-options').addEventListener('submit', submitForm);*/

let btnClicked;
$('#search-options').on('submit', (event) => {

  event.preventDefault();
  console.log(btnClicked);

  //const val = $("button[clicked=true]").val()


  /*$('.select-box, .btn').each(function (index) {
    const selectBox = $('.select-box')[index];
    const btn = $('.btn')[index];

    const eventType = $(selectBox).val();
    const typeOfSearch = $(btn).val();*/


    /*$.ajax({
      url: 'index.php',
      method: 'post',
      data: {
        eventType: eventType,
        btnEventType: typeOfSearch
      },
      success (response) {
      }
    });*/
  //});
});
$('.btn').on('click', event => {
    btnClicked = event.currentTarget;
  })

/*$('.btn').click(function (event) {
  console.log(event.target);
  $('#search-options').submit();
  event.preventDefault();
})*/
