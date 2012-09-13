<?xml version="1.0" encoding="UTF-8"?>
<!--
Ths transform produces an HTML data collection output of a DDI 1/2.x XML document

Author:	 IHSN
Version: MAY 2010
Platform: XSL 1.0

License: 
	Copyright 2010 The World Bank

    This program is free software; you can redistribute it and/or modify it under the terms of the
    GNU Lesser General Public License as published by the Free Software Foundation; either version
    2.1 of the License, or (at your option) any later version.
  
    This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
    without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
    See the GNU Lesser General Public License for more details.
  
    The full text of the license is available at http://www.gnu.org/copyleft/lesser.html
-->
<xsl:stylesheet version="1.0" xmlns:ddi="http://www.icpsr.umich.edu/DDI" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:include href="gettext.xslt"/>
	<xsl:output method="html"/>
	<!-- Setting the $wrapper parameter to "div" will surround the content with a <div> instead of a standalone <html> page
	     Use the <div> setiing if this is to be included in an extsining page -->
	<xsl:param name="wrapper">div</xsl:param>
	
	<!-- HTML styles -->
	<xsl:variable name="table_style">border:0px;border-collapse:collapse;padding:0px;margin-bottom:20px;</xsl:variable>
	<xsl:variable name="table_td_style">border:1px solid black;padding:5px;</xsl:variable>
	<xsl:variable name="table_th1_style">background:gainsboro;border:1px solid black;font-size:14px;padding:5px;</xsl:variable>
	<xsl:variable name="table_th2_style">background:silver;border:1px solid black;font-size:18px;padding:5px;margin-bottom:10px;</xsl:variable>
	<xsl:variable name="h5_style">text-decoration: underline;display:block</xsl:variable>

	<xsl:output method="xml" version="1.0" encoding="UTF-8" indent="yes"/>
	
	<xsl:template match="/">
		<xsl:choose>
			<xsl:when test="$wrapper='div'">
				<!-- Wrap in DIV -->
				<div>
					<xsl:apply-templates select="ddi:codeBook"/>
				</div>
			</xsl:when>
			<xsl:otherwise>
				<!-- Wrap in HTML -->
				<html>
					<head>
					<title><xsl:value-of select="//ddi:stdyDscr/ddi:citation/ddi:titlStmt/ddi:titl"/>
							(<xsl:value-of select="//ddi:stdyDscr/ddi:citation/ddi:titlStmt/ddi:altTitl"/>)</title>
						<style>
							td,th, table, p, body{font-family:arial;font-size:12px;}
							.table1{border-collapse: collapse;padding:0px;margin-bottom:20px;width:100%;}
							.table1 td{border:1px solid black;padding:5px;}
							.th1{background:gainsboro;border:1px solid black;font-size:12px;padding:5px;}
							.th2{background:silver;border:1px solid black;font-size:20px;padding:5px;margin-bottom:10px;}					
							.h5{text-decoration: underline;display:block} 
						</style>
					</head>
					<body>
						<xsl:apply-templates select="ddi:codeBook"/>
					</body>
				</html>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<!-- ddi:codeBook -->
	<xsl:template match="ddi:codeBook">
				<!--<div style="text-align:right;"> <a style="text-decoration:none;" href="#" onclick="javascript:window.print();return false;"><img border="0" alt="" src="../images/print.gif"/> Print</a></div>-->
				<div class="xsl-title"><xsl:call-template name="gettext"><xsl:with-param name="msg">Data Collection</xsl:with-param></xsl:call-template></div>
                				
				<!--Data Collection -->
				<xsl:if test="//ddi:stdyDscr//ddi:sumDscr | ddi:stdyDscr//ddi:collMode | ddi:stdyDscr//ddi:collSitu | ddi:stdyDscr//ddi:resInstru | ddi:stdyDscr//ddi:actMin">
						<!--data collection dates -->
						<xsl:if test="ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:collDate">
							<xsl:apply-templates select="ddi:stdyDscr//ddi:sumDscr"/>
						</xsl:if>
						<!-- data collection modes -->
						<xsl:if test="ddi:stdyDscr//ddi:collMode">
							<xsl:apply-templates select="ddi:stdyDscr//ddi:collMode"/>
						</xsl:if>
						<!-- data collection notes -->
						<xsl:if test="ddi:stdyDscr//ddi:collSitu">
							<xsl:apply-templates select="ddi:stdyDscr//ddi:collSitu" mode="row">
									<xsl:with-param name="caption">Data Collection Notes</xsl:with-param>
									<xsl:with-param name="cols">1</xsl:with-param>
							</xsl:apply-templates>		
						</xsl:if>
                        
                        <!-- data collectors -->
						<xsl:if test="ddi:stdyDscr//ddi:method/ddi:dataColl/ddi:dataCollector">
							<xsl:apply-templates select="ddi:stdyDscr//ddi:method/ddi:dataColl" />
						</xsl:if>
                        								
						<!--supervision -->
						<xsl:if test="ddi:stdyDscr//ddi:actMin">
							<xsl:apply-templates select="ddi:stdyDscr//ddi:actMin" mode="row">
									<xsl:with-param name="caption">Supervision</xsl:with-param>
									<xsl:with-param name="cols">1</xsl:with-param>
							</xsl:apply-templates>	
						</xsl:if>	
				</xsl:if>
				<!-- End Data Collection -->
				
	</xsl:template>
	
	<!-- docsDscr -->
	<xsl:template match="ddi:docDscr">
		<xsl:apply-templates select="ddi:citation/ddi:titlStmt/ddi:titl"/>
		<br/>
		<xsl:value-of select="ddi:citation/ddi:titlStmt/ddi:IDNo"/>
	</xsl:template>
	<!--stdyDscr/citation/verStmt-->
	<xsl:template match="ddi:stdyDscr/ddi:citation/ddi:verStmt">
		Production	Date: <xsl:value-of select="ddi:version/@date"/>
		<br/>
		<xsl:value-of select="ddi:version"/>
		<xsl:if test="normalize-space(ddi:notes)">
			<br/>
			<br/>
			<u>Notes</u>
			<xsl:call-template name="lf2br">
				<xsl:with-param name="text" select="ddi:notes"/>
			</xsl:call-template>
		</xsl:if>
		<br/>
	</xsl:template>
	<!--ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:anlyUnit-->
	<xsl:template match="ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:anlyUnit">
		<xsl:value-of select="."/>
		<br/>
	</xsl:template>
	<!--ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:dataKind-->
	<xsl:template match="ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:dataKind">
		<xsl:value-of select="."/>
		<br/>
	</xsl:template>
	<!--primary investigators - ddi:stdyDscr//ddi:AuthEnty-->
	<xsl:template match="ddi:stdyDscr//ddi:AuthEnty">
		<xsl:value-of select="."/>		
		<xsl:if test="normalize-space(@affiliation)">
				, <xsl:value-of select="@affiliation"/>
		</xsl:if>
		<br/>
	</xsl:template>
	<!-- Funding agencies - stdyDscr//Funding Agencies -->
	<xsl:template match="ddi:stdyDscr//ddi:prodStmt/ddi:fundAg">
		<xsl:value-of select="."/>
		<xsl:if test="normalize-space(@abbr)">
			(<xsl:value-of select="@abbr"/>)
		</xsl:if>
		<xsl:if test="normalize-space(@role) ">
			, <xsl:value-of select="@role"/>
		</xsl:if>
		<br/>
	</xsl:template>
	<!--other producers - ddi:stdyDscr//ddi:prodStmt/ddi:producer-->
	<xsl:template match="ddi:stdyDscr//ddi:prodStmt/ddi:producer">
		<xsl:value-of select="."/>
		<xsl:if test="normalize-space(@abbr)">
				(<xsl:value-of select="@abbr"/>)
			</xsl:if>
		<xsl:if test="normalize-space(@affiliation)">
				, <xsl:value-of select="@affiliation"/>
		</xsl:if>
		<xsl:if test="normalize-space(@role)">
				, <xsl:value-of select="@role"/>
		</xsl:if>
		<br/>
	</xsl:template>

	<!-- sampling procedure - stdyDscr//sampProc-->
	<xsl:template match="ddi:stdyDscr//ddi:sampProc">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="text" select="."/>
		</xsl:call-template>
		<br/>
	</xsl:template>
	<!--deviations from sample design - stdyDscr//deviat-->
	<xsl:template match="ddi:deviat">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="text" select="."/>
		</xsl:call-template>
		<br/>
	</xsl:template>
	<!--weighting - ddi:stdyDscr//ddi:weight-->
	<xsl:template match="ddi:weight">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="text" select="."/>
		</xsl:call-template>
		<br/>
	</xsl:template>
	<!--response rate - ddi:stdyDscr//ddi:anlyInfo/ddi:respRate-->
	<xsl:template match="ddi:respRate">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="text" select="."/>
		</xsl:call-template>
		<br/>
	</xsl:template>
	<!-- ddi:abstract -->
	<xsl:template match="ddi:abstract">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="text" select="."/>
		</xsl:call-template>
		<br/>
	</xsl:template>
	<!-- ddi:notes -->
	<xsl:template match="ddi:notes">
		<xsl:call-template name="lf2br">
			<xsl:with-param name="text" select="."/>
		</xsl:call-template>
		<br/>
	</xsl:template>

	<!-- Data Collection -->
	
	<!-- date collection dates -->
	<xsl:template match="ddi:stdyDscr//ddi:sumDscr">

		<xsl:if test="ddi:collDate/@event | ddi:collDate/@date">
<!--			<tr valign="top">
				<td colspan="2">-->
                	<div class="xsl-subtitle"><xsl:call-template name="gettext"><xsl:with-param name="msg">Data Collection Dates</xsl:with-param></xsl:call-template></div>
						<table border="0" class="data-collection" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
						<tr align="left">
							<th style="width:100px"><xsl:call-template name="gettext"><xsl:with-param name="msg">Start</xsl:with-param></xsl:call-template></th>
							<th style="width:100px"><xsl:call-template name="gettext"><xsl:with-param name="msg">End</xsl:with-param></xsl:call-template></th>
							<th><xsl:call-template name="gettext"><xsl:with-param name="msg">Cycle</xsl:with-param></xsl:call-template></th>
						</tr>	
						<!-- get all collection start dates -->
						<xsl:for-each select="ddi:collDate">
							<xsl:variable name="cycle" select="@cycle"/>
							<xsl:variable name="position" select="position()"/>
							<xsl:if test="@event='start'">
								<tr align="left">		
								<td><xsl:value-of select="@date"/></td>
								<td><xsl:value-of select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:collDate[@event='end' and position() > $position  and @cycle=$cycle]/@date"/></td>
									<td>
										<xsl:choose>
											<xsl:when test="normalize-space(@cycle)">
													<xsl:value-of select="@cycle"/>
											</xsl:when>
											<xsl:otherwise>N/A</xsl:otherwise>
										</xsl:choose>
									</td>
								</tr>
								</xsl:if>
						</xsl:for-each>
						
						<!-- no longer needed - prints all start/end dates with no cycle info
						
						<xsl:if test="ddi:collDate[@event='start' and normalize-space(@cycle)='']">
								<tr valign="top">		
								<td>
								<xsl:for-each select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:collDate[normalize-space(@cycle)=''][@event='start']">
										<xsl:value-of select="@date"/>
								</xsl:for-each>
								</td>
								<td>
								<xsl:for-each select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:collDate[normalize-space(@cycle)=''][@event='end']">
										<xsl:value-of select="@date"/>
								</xsl:for-each>
								</td>
								<td>N/A</td>
								</tr>														
						</xsl:if>
						-->
					</table>
<!--				</td>
			</tr>-->
		</xsl:if>	
		<!-- time periods -->
		<xsl:if test="ddi:timePrd/@event | ddi:timePrd/@date">
			<tr valign="top">
				<td colspan="2">
                	<div class="xsl-subtitle"><xsl:call-template name="gettext"><xsl:with-param name="msg">Time Periods</xsl:with-param></xsl:call-template></div>
						<table border="0" class="data-collection" cellpadding="0" cellspacing="0" style="border-collapse:collapse">
						<tr align="left">
							<th style="width:100px"><xsl:call-template name="gettext"><xsl:with-param name="msg">Start</xsl:with-param></xsl:call-template></th>
							<th style="width:100px"><xsl:call-template name="gettext"><xsl:with-param name="msg">End</xsl:with-param></xsl:call-template></th>
							<th><xsl:call-template name="gettext"><xsl:with-param name="msg">Cycle</xsl:with-param></xsl:call-template></th>
						</tr>	
						<!-- get all collection start dates -->
						<xsl:for-each select="ddi:timePrd[@event='start' and normalize-space(@cycle)!='']">
							<xsl:variable name="cycle" select="@cycle"/>
							<xsl:variable name="position" select="position()"/>
								<tr align="left">		
								<td><xsl:value-of select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:timePrd[@event='start'][@cycle=$cycle]/@date"/></td>
								<td><xsl:value-of select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:timePrd[@event='end'][@cycle=$cycle and position() > $position]/@date"/></td>
									<td>
										<xsl:choose>
											<xsl:when test="normalize-space(@cycle)">
													<xsl:value-of select="@cycle"/>
											</xsl:when>
											<xsl:otherwise>N/A</xsl:otherwise>
										</xsl:choose>
									</td>
								</tr>														
						</xsl:for-each>
						<xsl:if test="ddi:timePrd[@event='start' and normalize-space(@cycle)='']">
								<tr valign="top">		
								<td>
								<!-- print all start dates -->
								<xsl:for-each select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:timePrd[normalize-space(@cycle)=''][@event='start']">
										<xsl:value-of select="@date"/>
								</xsl:for-each>
								</td>
								<td>
								<!-- print all end dates -->
								<xsl:for-each select="//ddi:codeBook/ddi:stdyDscr/ddi:stdyInfo/ddi:sumDscr/ddi:timePrd[normalize-space(@cycle)=''][@event='end']">
										<xsl:value-of select="@date"/>
								</xsl:for-each>
								</td>
								<td>N/A</td>
								</tr>														
						</xsl:if>						
					</table>
				</td>
			</tr>
		</xsl:if>	
	</xsl:template>

	<!-- Data Collection Mode -->
	<xsl:template match="ddi:stdyDscr//ddi:collMode">
			<div class="xsl-subtitle"><xsl:call-template name="gettext"><xsl:with-param name="msg">Data Collection Mode</xsl:with-param></xsl:call-template></div>
			<xsl:value-of select="."/>
	</xsl:template>	

	<!-- Questionnaires-->
	<xsl:template match="ddi:stdyDscr//ddi:resInstru">
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text" select="."/>
				</xsl:call-template>
	</xsl:template>

	<!-- Data Collection Notes-->
	<xsl:template match="ddi:stdyDscr//ddi:collSitu">
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text" select="."/>
				</xsl:call-template>
	</xsl:template>

	<!-- Data Collectors-->
	<xsl:template match="ddi:stdyDscr//ddi:method/ddi:dataColl">
		<div class="xsl-subtitle" style="margin-bottom:10px;">
		<xsl:call-template name="gettext"><xsl:with-param name="msg">Data Collectors</xsl:with-param></xsl:call-template>
		</div>
    	<table class="xsl-table">
			<tr>
                <th><xsl:call-template name="gettext"><xsl:with-param name="msg">Name</xsl:with-param></xsl:call-template></th>
                <th><xsl:call-template name="gettext"><xsl:with-param name="msg">Abbreviation</xsl:with-param></xsl:call-template></th>
                <th><xsl:call-template name="gettext"><xsl:with-param name="msg">Affiliation</xsl:with-param></xsl:call-template></th>
            </tr>
    	<xsl:for-each select="ddi:dataCollector">
			<tr>
            	<td><xsl:value-of select="."/></td>
                <td><xsl:value-of select="@abbr"/></td>
                <td><xsl:value-of select="@affiliation"/></td>
            </tr>            
        </xsl:for-each>
        </table>
	</xsl:template>



	<!-- Supervision-->
	<xsl:template match="ddi:stdyDscr//ddi:actMin">
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text" select="."/>
				</xsl:call-template>
	</xsl:template>
	<!-- End Data Collection -->

	<!-- Data Processing & Appraisal -->
	<!-- Data Editing -->
	<xsl:template match="ddi:stdyDscr//ddi:cleanOps">
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text" select="."/>
				</xsl:call-template>
	</xsl:template>	
	<!-- Estimate of Sampling Error -->
		<xsl:template match="ddi:stdyDscr//ddi:EstSmpErr">
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text" select="."/>
				</xsl:call-template>
	</xsl:template>	
		<!-- Other Forms of Data Appraisal -->
		<xsl:template match="ddi:stdyDscr//ddi:dataAppr">
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text" select="."/>
				</xsl:call-template>
	</xsl:template>
<!-- End Data Processing & Appraisal -->	


	<!-- 
		utility templates/functions 
	-->
	<!-- this template can be call by call-template of by match -->
	<xsl:template match="*" name="row" mode="row">
		<xsl:param name="caption"/>
		<xsl:param name="text"/>
		<xsl:param name="cols" select="1"/>
		<xsl:variable name="label">
			<xsl:call-template name="gettext"><xsl:with-param name="msg"><xsl:value-of select="$caption"/></xsl:with-param></xsl:call-template>
			<xsl:choose>
				<xsl:when test="position()>1"> (<xsl:value-of select="position()"/>)</xsl:when>
				<xsl:otherwise>
					<xsl:if test="name(following-sibling::*[1])=name()"> (1)</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		
			<xsl:choose>
				<xsl:when test="$cols=2">
					<!-- 2-columns -->
				<tr valign="top">
					<td >
						<xsl:value-of select="$label"/>
					</td>
					<td >
						<xsl:choose>
							<xsl:when test="normalize-space($text)">
								<xsl:call-template name="lf2br">
									<xsl:with-param name="text" select="$text"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<xsl:apply-templates select="."/>
							</xsl:otherwise>
						</xsl:choose>
					</td>
					</tr>
				</xsl:when>
				<xsl:otherwise>
					<!-- 1-column -->
						<!--<td  colspan="2">-->
						<div class="xsl-caption">
							<xsl:value-of select="$label"/>
						</div>
						<xsl:choose>
							<xsl:when test="normalize-space($text)">
								<xsl:call-template name="lf2br">
									<xsl:with-param name="text" select="$text"/>
								</xsl:call-template>
							</xsl:when>
							<xsl:otherwise>
								<xsl:apply-templates select="."/>
							</xsl:otherwise>
						</xsl:choose>
					<!--</td>-->
				</xsl:otherwise>
			</xsl:choose>
	</xsl:template>

	<!-- Function/template: converts line feed to break line <BR> for html display -->
	<xsl:template name="lf2br">
		<xsl:param name="text"/>
		<xsl:choose>
			<xsl:when test="contains($text,'&#10;')">
				<xsl:value-of select="substring-before($text,'&#10;')"/>
				<br/>
				<xsl:call-template name="lf2br">
					<xsl:with-param name="text">
						<xsl:value-of select="substring-after($text,'&#10;')"/>
					</xsl:with-param>
				</xsl:call-template>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$text"/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>	
	<!-- end utility functions/templates -->
</xsl:stylesheet>
