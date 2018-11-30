	<!-- #BeginEditable "content" -->
	<table border="0" cellspacing="0" width="100%" cellpadding="1">
		<!-- -->
		<tr>
			<td valign="top">
			ADMINMENU
				<img src="/images/links.gif"> &nbsp;
				<a href="index.php?action=welcome" class="rvts5">Library Home</a> &nbsp;
				<a href="index.php?action=help" class="rvts5">Help</a> &nbsp;
				<a href="index.php?action=login" class="rvts5">Volunteer Log In</a> &nbsp;
				ADMINSTATUS
			</td>
		</tr>
		<!-- -->
		<tr>
			<td valign="top">
				<form name="search" method="get" action="search.php">
				<img src="http://www.v-excel.org/images/browse.gif">&nbsp;
				<table cellspacing="0" cellpadding="10" border="0" class="yellow" width="100%">
					<tr>
						<td>
							By Media type:<br>
							MEDIAMENU
						</td>
						<td>
							or by Subject:<br>
							SUBJECTMENU
						</td>
						<td>
							<input type="submit" name="submit" class="mainoption" value="Browse">
						</td>
					</tr>
				</table>
				</form>
			</td>
		</tr>
		<tr>
			<td valign="top">
				<img src="http://www.v-excel.org/images/search.gif"><br>
				<table cellspacing="0" cellpadding="10" border="0" class="yellow" width="100%">
					<tr>
						<td>
							<form name="search" method="get" action="search.php">
							Search by:&nbsp;
							<input type="radio" name="search_type" value="title"TITLECHECKED>
							Title&nbsp;
							<input type="radio" name="search_type" value="author"AUTHORCHECKED>
							Author &nbsp;
							<input type="text" name="search_text" value="SEARCHTEXT" class="mb" size=10 maxlength=50>&nbsp;
							<input type="submit" name="submit" class="mainoption" value="Search">
							</form>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
