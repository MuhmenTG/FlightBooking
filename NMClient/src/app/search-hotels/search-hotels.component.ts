import { Component } from '@angular/core';
import { HotelService } from '../_services/hotel.service';
import { NgForm } from '@angular/forms';
import { SearchHotelsRequest } from '../_models/Hotels/SearchHotelsRequest';
import { HotelResponse } from '../_models/Hotels/HotelResponse';

@Component({
  selector: 'app-search-hotels',
  templateUrl: './search-hotels.component.html',
  styleUrls: ['./search-hotels.component.css']
})
export class SearchHotelsComponent {
  peopleAmount = [1, 2, 3, 4, 5];
  roomAmount = ["1", "2", "3", "4"];
  model: SearchHotelsRequest = { cityCode: '', adults: this.peopleAmount[0], checkInDate: new Date(), checkOutDate: new Date(), roomQuantity: this.roomAmount[0], priceRange: '', paymentPolicy: '', boardType: '' }
  hotelResponses: HotelResponse[] = [];
  formSubmitted = false;
  todayDate = Date.now();

  constructor(private _hotelService: HotelService) { }

  submitForm(form: NgForm) {
    if (!form.valid) {
      return alert("Form is not valid. Try again.");
    } else {
      this._hotelService.getHotels(this.model).subscribe(responses => {
        this.hotelResponses = [];
        responses.forEach(hotelOffer => {
          this.hotelResponses.push(hotelOffer);
          this.formSubmitted = true;
        });
      })
    }
  }
}
