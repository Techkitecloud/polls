<!--
  - @copyright Copyright (c) 2018 René Gieling <github@dartcafe.de>
  -
  - @author René Gieling <github@dartcafe.de>
  -
  - @license GNU AGPL version 3 or any later version
  -
  - This program is free software: you can redistribute it and/or modify
  - it under the terms of the GNU Affero General Public License as
  - published by the Free Software Foundation, either version 3 of the
  - License, or (at your option) any later version.
  -
  - This program is distributed in the hope that it will be useful,
  - but WITHOUT ANY WARRANTY; without even the implied warranty of
  - MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  - GNU Affero General Public License for more details.
  -
  - You should have received a copy of the GNU Affero General Public License
  - along with this program.  If not, see <http://www.gnu.org/licenses/>.
  -
  -->

<template lang="html">
	<Actions default-icon="icon-download">
		<ActionButton close-after-click @click="exportFile('xlsx')">
			<template #icon>
				<ExcelIcon />
			</template>
			{{ t('polls', 'Download Excel spreadsheet') }}
		</ActionButton>

		<ActionButton close-after-click @click="exportFile('ods')">
			<template #icon>
				<FileTableIcon />
			</template>
			{{ t('polls', 'Download Open Document spreadsheet') }}
		</ActionButton>

		<ActionButton close-after-click @click="exportFile('csv')">
			<template #icon>
				<CsvIcon />
			</template>
			{{ t('polls', 'Download CSV file') }}
		</ActionButton>

		<ActionButton close-after-click @click="exportFile('html')">
			<template #icon>
				<XmlIcon />
			</template>
			{{ t('polls', 'Download HTML file') }}
		</ActionButton>
	</Actions>
</template>

<script>
import { mapGetters, mapState } from 'vuex'
import { saveAs } from 'file-saver'
import XLSX from 'xlsx'
import { Actions, ActionButton } from '@nextcloud/vue'
import ExcelIcon from 'vue-material-design-icons/MicrosoftExcel.vue'
import FileTableIcon from 'vue-material-design-icons/FileTableOutline.vue'
import CsvIcon from 'vue-material-design-icons/FileDelimited.vue'
import XmlIcon from 'vue-material-design-icons/Xml.vue'

export default {
	name: 'ExportPoll',
	components: {
		Actions,
		ActionButton,
		CsvIcon,
		ExcelIcon,
		FileTableIcon,
		XmlIcon,
	},

	data() {
		return {
			workBook: [],
			sheetData: [],
		}
	},

	computed: {
		...mapGetters({
			participants: 'poll/participants',
			getVote: 'votes/getVote',
			explodeDates: 'options/explodeDates',
		}),
		...mapState({
			poll: (state) => state.poll,
			options: (state) => state.options,
			votes: (state) => state.votes,
		}),
	},

	methods: {
		exportFile(type) {
			this.workBook = XLSX.utils.book_new()
			this.workBook.SheetNames.push(this.poll.title)
			this.sheetData = []

			if (['html', 'xlsx', 'ods'].includes(type)) {
				this.sheetData.push(
					[this.poll.title],
					[this.poll.description],
				)
			}

			if (this.poll.type === 'textPoll') {
				this.sheetData.push(['', ...this.options.list.map((item) => item.text)])

			} else if (['csv'].includes(type)) {
				this.sheetData.push([t('polls', 'Participants'), ...this.options.list.map((option) => this.explodeDates(option).iso)])

			} else if (['html'].includes(type)) {
				this.sheetData.push([t('polls', 'Participants'), ...this.options.list.map((option) => this.explodeDates(option).raw)])

			} else {
				this.sheetData.push([t('polls', 'From'), ...this.options.list.map((option) => this.explodeDates(option).from.dateTime)])
				this.sheetData.push([t('polls', 'To'), ...this.options.list.map((option) => this.explodeDates(option).to.dateTime)])
			}

			if (['html', 'ods', 'xlsx'].includes(type)) {
				this.addVotesArray('symbols')
			} else if (['csv'].includes(type)) {
				this.addVotesArray('raw')
			} else {
				this.addVotesArray()
			}

			const workBookOutput = XLSX.write(this.workBook, { bookType: type, type: 'binary' })
			saveAs(new Blob([this.s2ab(workBookOutput)], { type: 'application/octet-stream' }), `poll.${type}`)
		},

		addVotesArray(style) {
			this.participants.forEach((participant) => {
				const votesLine = [participant.displayName]

				this.options.list.forEach((option, i) => {
					if (style === 'symbols') {
						votesLine.push(this.getVote({ userId: participant.userId, option }).answerSymbol ?? '❌')
					} else if (style === 'raw') {
						votesLine.push(this.getVote({ userId: participant.userId, option }).answer)
					} else {
						votesLine.push(this.getVote({ userId: participant.userId, option }).answerTranslated ?? t('polls', 'No'))
					}
				})

				this.sheetData.push(votesLine)
			})

			const workSheet = XLSX.utils.aoa_to_sheet(this.sheetData)
			this.workBook.Sheets[this.poll.title] = workSheet
		},

		s2ab(s) {
			const buf = new ArrayBuffer(s.length) // convert s to arrayBuffer
			const view = new Uint8Array(buf) // create uint8array as viewer
			for (let i = 0; i < s.length; i++) view[i] = s.charCodeAt(i) & 0xFF // convert to octet
			return buf
		},
	},
}
</script>
