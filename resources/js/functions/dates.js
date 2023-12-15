export function getDateString(date) {
    const parsedDate = new Date(Date.parse(date));
    return parsedDate.toLocaleDateString('en-PH', { year: 'numeric', month: 'long', day: 'numeric' });
}

export function calculateIfEdited(created_date, updated_date){
    this.created_date = new Date(Date.parse(created_date));
    this.updated_date = new Date(Date.parse(updated_date))

    return updated_date > created_date;
}