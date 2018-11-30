<h2>Volunteer Help</h2>

<h2>Volunteer Functions</h2>

<ul type="square">
  <li><a href="#signout">Signing Out a Resource</a></li>
  <li><a href="#signin">Marking a Resource as Returned</a></li>
</ul>

<h2>Librarian Functions</h2>

<ul type="square">
  <li><a href="#add">Adding a New Resource</a></li>
  <li><a href="#update">Updating an Existing Resource</a></li>
  <li><a href="#export">Exporting the Database</a></li>
  <li><a href="#delete">Deleting an Existing Resource</a></li>
  <li><a href="#password">Changing the Volunteer Password and 
Login</a></li>
  <li><a href="#fields">Various Field Definitions</a></li>
</ul>

<a name="signout"></a><h2>Signing Out a Resource</h2>

<ol type="1">
  <li>Search or Browse the database for the first resource to be signed
out.</li>
  <li>Click on the <i>View</i> link to see the entire record.</li>

  <p><b>Not all resources can be signed-out.</b> If the <i>Status</i> 
of the book is &ldquo;On Loan&rdquo;, &ldquo;On Reserve&rdquo;, 
&ldquo;Missing&rdquo;, or &ldquo;Restricted&rdquo; you cannot sign the 
resource out. Each of these states is <a href="#fields">described 
below</a>.</p>

  <li>If the resource status is &ldquo;On Shelf&rdquo; then follow the
link called <i>Check-out Resource</i>.</li>
  <li>Enter the Borrower's name and one piece of contact information, 
either telephone number or email address.</li>
  <li>Click on the <i>Check-out</i> button.</li>
</ol>

<p>A message will display if the check-out was successful. You can further
verify this by following the link in the Admin box labeled <i>Returns by
Name</i>. Also, remember to remind the borrower of the due date.</p>

<p>To sign out multiple resources, repeat the above steps. You will 
have to enter the same contact information for each resource.</p>

<a name="signin"></a><h2>Marking a Resource as Returned</h2>

<ol type="1">
  <li>Click the link called <i>Returns by Name</i> (or title, or 
date).</li>
  <li>Find the first resource to be recorded as returned and click on the 
<i>View</i> link to see the entire record.</li>
  <li>Click on the link labeled <i>Mark Resource as Returned</i>.</li>
</ol>

<p>A message will display if the check-in was successful. You can further
verify this by following the link in the Admin box labeled <i>Returns by
Name</i>. The resource should no longer appear in the list.</p>

<p>To sign-in multiple resources, repeat the above steps.</p>

<a name="#add"></a><h2>Adding a New Resource</h2>

<ol type="1">
  <li>Click the link called <i>Add New Resource</i>.</li>
  <li>Fill in all available information on the resource.</li>
  <li>Click on the <i>Add</i> button.</li>
</ol>

<p>See the <a href="#fields">notes below</a> on each of the fields used in
the add resource page.</p>

<a name="#update"></a><h2>Updating an Existing Resource</h2>

<ol type="1">
  <li>Search or Browse the database for the resource to be updated.</li>
  <li>Click on the <i>View</i> link to see the entire record.</li>
  <li>Click on the <i>Update Record</i> link.</li>
  <li>Update all available information on the resource.</li>
  <li>Click on the <i>Update</i> button.</li>
</ol>

<p>See the <a href="#fields">notes below</a> on each of the fields used 
in the update page.</p>

<a name="#export"></a><h2>Exporting the Database</h2>

<p>From time to time, it may be handy to view the database using a 
spreadsheet. The following steps will export the database in 
comma-separated values (CSV) format.

<ol type="1">
  <li>Click the link called <i>Export Resource Table</i> or 
<i>Export Loan Table</i>.</li>
  <li>The output will appear in a new window.</li>
  <li>Copy the contents of the new window and paste into a text 
editor and save with the extension CSV.</li>
  <li>Import the file in any popular spreadsheet.</li>
</ol>

<p>The database administrator can import the spreadsheet back into the 
Library database. This may be particularly useful when executing mass 
updates to the database records.</p>

<a name="#delete"></a><h2>Deleting an Existing Resource</h2>

<p>You cannot delete an existing resource from the database. This feature 
reduces the risk of accidental data loss. If a resource is entered in 
error, contact the database administrator to have the record removed.</p>

<a name="#password"></a><h2>Changing the Volunteer Password and Login</h2>

<p>You cannot change the volunteer password and login. Contact the 
database administrator to have this information updated.</p>

<a name="#fields"></a><h2>Various Field Definitions</h2>

<p>When entering data in the database, the following information explains 
the intended use for each field.

<p><b>Resource Number</b></p>

<p>A number assigned by the system so that each resource can be uniquely
identified. This is the &ldquo;Library Number&rdquo; should the books be
labeled on the inside cover or bar coded.</p>

<p><b>Location</b></p>

<p>A text field denoting the room where the resource is physically
shelved. This feature allows the collection to be stored in various
rooms.</p>

<p>New locations can be set up by contacting the Database
Administrator.</p>

<p><b>Media Type</b></p>

<table border>
<tr>
  <td>Label</td>
  <td>Description</td>
</tr>
<tr>
  <td>Book</td>
  <td>Books, obviously.</td>
</tr>
<tr>
  <td>Periodical</td>
  <td>Magazines, Newspapers, and Newsletters.</td>
</tr>
<tr>
  <td>Video</td>
  <td>Motion pictures and informational videos stored as VHS or DVD.</td>
</tr>
<tr>
  <td>Music</td>
  <td>Audio recordings stored as cassette tapes or CDs.</td>
</tr>
<tr>
  <td>Hanging File</td>
  <td>Hanging files are stored filing cabinets and contain loose flyers,
news clippings, informational pamphlets, and various records.</td>
</tr>
</table>

<p>New media types can be set up by contacting the Database 
Administrator.</p>

<p><b>Status</b></p>

<table border>
<tr>
  <td>Label</td>
  <td>Description</td>
</tr>
<tr>
  <td>On Shelf</td>
  <td>Located in the Library and available to be borrowed by the 
general public.</td>
</tr>
<tr>
  <td>On Loan</td>
  <td>Borrowed by a patron and due back within the next three weeks.</td>
</tr>
<tr>
  <td>On Reserve</td>
  <td>Available for use in the library by the general public, but
<b>this resource is not to leave the library</b>.</td>
</tr>
<tr>
  <td>Missing</td>
  <td>This resource exists in the database but cannot be found in the 
library. This category designates books that were never returned or 
ones that otherwise have gone missing.</td>
</tr>
<tr>
  <td>Restricted</td>
  <td><b>Potentially sensitive information</b> which is <b>not available
for public viewing</b>, but is catalogued for completeness.</td>
</tr>
</table>

<p><b>Subject</b></p>

<p>Subjects are assigned by the Librarian based on his or her best 
knowledge of the resource. Most subjects apply to only one media type. 
Consult a list of existing resources under that subject before assigning 
it to a new resource.</p>

<p>New subjects can be set up by contacting the Database 
Administrator.</p>

<p><b>Title</b></p>

<p>A 100-character field used to store the resource title. Avoid 
abbreviations wherever possible.</p>

<p><b>Author</b></p>

<p>A 50-character field used to store the name(s) of the author(s) or 
editor(s).</p>

<p><b>Year</b></p>

<p>A 4-character field used to store the year of publishing/copyright.</p>

<p><b>ISBN</b></p>

<p>A 20-character field used to store the 10-digit ISBN number (for
books), the 8-digit ISSN number (for periodicals), or the UPC for videos
and other marketed goods.</p>

<p><b>Comments</b></p>

<p>A 50-character field used to store any pertinent information about the 
resource. <b>This field can be viewed by anyone visiting the site</b>.</p>

<p><b>Date Acquired</b></p>

<p>A 12-character field used to store the time period the resource was 
acquired, if known. This is a field viewable only by volunteers.</p>

<p><b>Donated By</b></p>

<p>A 20-character field used to store the name of the person who donated 
the resource, if known. This field is viewable only by volunteers.</p>
