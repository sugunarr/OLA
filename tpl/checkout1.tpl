                <p>Resource will be due back on <b>DUEDATE</b></p>
                <form name="checkout" method="get" action="checkout2.php">
                <table class="type2">
                  <tr>
                    <td class="type3">Resource Number</td>
                    <td>RESID<input type="hidden" name="id" value="RESID"></td>
                  </tr>
                  <tr>
                    <td class="type3">Title</td>
                    <td>TITLE</td>
                  </tr>
                  <tr>
                    <td class="type3">Author</td>
                    <td>AUTHOR</td>
                  </tr>
                  <tr>
                    <td class="type3">Borrower's Name</td>
                    <td><input type="text" name="person_name" size=31 maxlength=31> (* Required)</td>
                  </tr>
                  <tr>
                    <td class="type3">Contact Information</td>
                    <td><input type="text" name="person_contact_info" size=31 maxlength=31> (* Required) [Phone Number, or Email Address]</td>
                  </tr>
                  <tr>
                    <td class="type3">Comments</td>
                    <td><input type="text" name="comments" size=31 maxlength=31></td>
                  </tr>
                </table>
                <br>
                <input type="submit" name="submit" value="Checkout">
                </form>

                <p><a href="index.php">Click here</a> to abandon this form
                and return to the main volunteer menu.</p>
