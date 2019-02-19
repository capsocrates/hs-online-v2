
	function diceRoller(dice)
	{
		//Display symbols if Valkyrie dice are checked
		if ($("#valDice").attr("checked")) {
			valDice = 1;
		} else {
			valDice = 0;
		}
		$.post("rolldice.php", { roll: dice, val: valDice } );
	}

	function diceRollerD(dice)
	{
		//Dislay symbols if Valkyrie dice are checked
		if ($("#valDice").attr("checked")) {
			valDice = 1;
		} else {
			valDice = 0;
		}
		$.post("rolldiceD.php", { roll: dice, val: valDice } );
	}

	function resetdice ()
	{
		document.getElementById("skull").value = 0;
		document.getElementById("shield").value = 0;
		document.getElementById("blank").value = 0;
		document.getElementById("twenty").value = 0;
		document.getElementById("twenty").innerHTML = document.getElementById("twenty").value
		document.getElementById("skull").innerHTML = document.getElementById("skull").value
		document.getElementById("shield").innerHTML = document.getElementById("shield").value
		document.getElementById("blank").innerHTML = document.getElementById("blank").value
		document.getElementById("adddice").checked=false
	}

	function resetom ()
	{
		document.getElementById("om1").value = "";
		document.getElementById("om2").value = "";
		document.getElementById("om3").value = "";
		document.getElementById("omx").value = "";
		document.getElementById("om1").innerHTML = document.getElementById("om1").value
		document.getElementById("om2").innerHTML = document.getElementById("om2").value
		document.getElementById("om3").innerHTML = document.getElementById("om3").value
		document.getElementById("omx").innerHTML = document.getElementById("omx").value
	}


	function roll (dtmp)
	{
		diceRoller (dtmp);
	}

	function rerollskulls ()
	{
		var tmp = document.getElementById("skull").value
		document.getElementById("skull").value = 0;
		diceRoller (tmp);
	}

	function rerollshields ()
	{
		var tmp = document.getElementById("shield").value
		document.getElementById("shield").value = 0;
		diceRoller (tmp);
	}

	function rerollblanks ()
	{
		var tmp = document.getElementById("blank").value
		document.getElementById("blank").value = 0;
		diceRoller (tmp);
	}

	function d20roll()
	{
		$.post("rolltwenty.php");	
	}


	 function toggle(id) {
			var state = document.getElementById(id).style.display;
				if (state == 'block') {
					document.getElementById(id).style.display = 'none';
				} else {
					document.getElementById(id).style.display = 'block';
				}
	}

	</script>
	<script type="text/javascript">
	function validateForm()
	{
	var x=document.forms["form"]["name"].value;

	if (x==null || x=="" || x.charAt("0") == " ")
	  {
	  alert("Please enter a name.");
	  return false;
	  }
	}
	function doneTurn()
	{
		$.post("time-done.php");
	}

