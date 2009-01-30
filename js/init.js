$(document).ready(function () {
	$("#TICKETS").toggle(
		function () {
			$('#ticketMenu').css("visibility", "visible");
			$('#ticketMenu').css("display", "block");
		},
		function () {
			$('#ticketMenu').hide();
		});
	$("#TICKETMENUUNI").toggle(
		function () {
			$('#ticketMenuUniq').css("visibility", "visible");
			$('#ticketMenuUniq').css("display", "block");
		},
		function () {
			$('#ticketMenuUniq').hide();
		});
 });

function thisTicketShow(){
			$('#ticketMenuUniq').css("visibility", "visible");
			$('#ticketMenuUniq').css("display", "block");
}
function thisTicketHide(){
			$('#ticketMenuUniq').hide();
}