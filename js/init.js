$(document).ready(function () {
	$("#TICKETS").toggle(
		function () {
			$('#ticketMenu').css("visibility", "visible");
			$('#ticketMenu').css("display", "block");
		},
		function () {
			$('#ticketMenu').hide();
		});
 });
