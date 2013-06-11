import View\Admin\Index

{{REPLACE body}}
	This is the data view
	<table>
		{{FOR data AS row}}
			<tr>
				{{FOR row AS value}}
					<td>{{value}}</td>
				{{ENDFOR}}
			</tr>
		{{ENDFOR}}
	</table>
{{ENDREPLACE}}